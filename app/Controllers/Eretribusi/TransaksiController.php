<?php

namespace App\Controllers\Eretribusi;

use App\Controllers\BaseController;
use App\Models\TransaksiRetribusiModel;
use App\Models\TransaksiItemModel;
use App\Models\JenisRetribusiModel;
use App\Models\PuskesmasModel;
use App\Models\PuskesmasJenisModel;
use App\Models\PasienModel;

class TransaksiController extends BaseController
{
    protected $transaksiModel;
    protected $itemModel;
    protected $jenisModel;
    protected $puskesmasModel;
    protected $puskesmasJenisModel;
    protected $pasienModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiRetribusiModel();
        $this->itemModel = new TransaksiItemModel();
        $this->jenisModel = new JenisRetribusiModel();
        $this->puskesmasModel = new PuskesmasModel();
        $this->puskesmasJenisModel = new PuskesmasJenisModel();
        $this->pasienModel = new PasienModel();
    }

    /**
     * List transactions for current puskesmas
     */
    public function index()
    {
        // Tenant Isolation
        if (session()->get('role') !== 'admin_kabupaten') {
            $idPuskesmas = session()->get('id_puskesmas');
        } else {
            $idPuskesmas = null;
        }

        $query = $this->transaksiModel
            ->select('transaksi_retribusi.*, puskesmas.prasarana, pasien.nama_pasien, pasien.no_rm')
            ->join('puskesmas', 'puskesmas.id = transaksi_retribusi.id_puskesmas', 'left')
            ->join('pasien', 'pasien.id = transaksi_retribusi.id_pasien', 'left');

        if ($idPuskesmas) {
            $query->where('transaksi_retribusi.id_puskesmas', $idPuskesmas);
        }

        $transaksiRaw = $query->orderBy('transaksi_retribusi.invoice_date', 'DESC')
            ->findAll();

        $totalTerbayar = 0;
        $totalBelumTerbayar = 0;
        $totalTransaksi = count($transaksiRaw);

        $transaksi = [];
        foreach ($transaksiRaw as $trx) {
            $items = $this->itemModel->getItemsByTransaksi($trx['id']);

            $currentAmount = 0;
            $itemNames = [];
            foreach ($items as $item) {
                $currentAmount += $item['amount'];
                $itemNames[] = $item['jenis'];
            }
            $trx['items_detail'] = $items;
            $trx['jenis'] = implode(', ', $itemNames);
            $trx['amount'] = $currentAmount;
            $trx['volume'] = count($items);
            $transaksi[] = $trx;

            if ($trx['status'] == 'paid') {
                $totalTerbayar += $currentAmount;
            } else {
                $totalBelumTerbayar += $currentAmount;
            }
        }

        return view('eretribusi/transaksi/index', [
            'transaksi' => $transaksi,
            'totalTerbayar' => $totalTerbayar,
            'totalBelumTerbayar' => $totalBelumTerbayar,
            'totalTransaksi' => $totalTransaksi
        ]);
    }

    /**
     * Show form to input transaction
     */
    public function create()
    {
        if (session()->get('role') !== 'admin_kabupaten') {
            $idPuskesmas = session()->get('id_puskesmas');
        } else {
            $idPuskesmas = $this->request->getGet('id_puskesmas');
            if (!$idPuskesmas) {
                return redirect()->to('/eretribusi/transaksi')->with('notif_gagal', 'Pilih puskesmas terlebih dahulu.');
            }
        }

        $allowedJenisIds = $this->puskesmasJenisModel->getJenisIdsByPuskesmas($idPuskesmas);
        $jenis = empty($allowedJenisIds) ? [] : $this->jenisModel->whereIn('id', $allowedJenisIds)->findAll();

        $puskesmas = [];
        if (session()->get('role') === 'admin_kabupaten') {
            $puskesmas = $this->puskesmasModel->findAll();
        }

        $currentPuskesmas = null;
        if ($idPuskesmas && session()->get('role') !== 'admin_kabupaten') {
            $currentPuskesmas = $this->puskesmasModel->find($idPuskesmas);
        }

        return view('eretribusi/transaksi/create', [
            'tarif' => $jenis,
            'jenis' => $jenis,
            'puskesmas' => $puskesmas,
            'currentPuskesmas' => $currentPuskesmas,
            'selectedPuskesmasId' => $idPuskesmas
        ]);
    }

    /**
     * Save transaction + pasien to DB
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'no_dokumen'    => 'required',
            'nama_pasien'   => 'required',
            'jenis_kelamin' => 'required',
            'tgl_lahir'     => 'required',
            'id_jenis.*'    => 'required|numeric',
            'volume.*'      => 'required|numeric|greater_than[0]',
        ]);

        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $idPuskesmas = session()->get('role') !== 'admin_kabupaten'
            ? session()->get('id_puskesmas')
            : $this->request->getPost('id_puskesmas');

        $noDokumen    = $this->request->getPost('no_dokumen');
        $namaPasien   = $this->request->getPost('nama_pasien');
        $alamatPasien = $this->request->getPost('alamat_pasien');
        $jenisKelamin = $this->request->getPost('jenis_kelamin');
        $tglLahir     = $this->request->getPost('tgl_lahir');
        $idJenisArr   = $this->request->getPost('id_jenis');
        $volumeArr    = $this->request->getPost('volume');

        // Generate unique invoice number
        $invoice = $this->generateInvoiceNumber($idPuskesmas);

        // Simpan / update data pasien ke tabel terpisah
        $idPasien = $this->pasienModel->createOrUpdate($noDokumen, [
            'nama_pasien'   => $namaPasien,
            'alamat_pasien' => $alamatPasien,
            'jenis_kelamin' => $jenisKelamin,
            'tgl_lahir'     => $tglLahir,
        ]);

        // Simpan transaksi
        $db = \Config\Database::connect();
        $db->transStart();

        $idTransaksi = $this->transaksiModel->insert([
            'id_puskesmas'  => $idPuskesmas,
            'id_pasien'     => $idPasien,
            'no_dokumen'    => $noDokumen,
            'invoice'       => $invoice,
            'invoice_date'  => date('Y-m-d'),
            'status'        => 'pending'
        ]);

        $totalAmount = 0;
        foreach ($idJenisArr as $key => $idJenis) {
            $volume = (float) $volumeArr[$key];

            if (!$this->puskesmasJenisModel->isAllowed($idPuskesmas, (int)$idJenis)) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('notif_gagal', 'Terdapat jenis layanan yang tidak diizinkan untuk Puskesmas ini.');
            }

            $tarifData = $this->jenisModel->find($idJenis);
            $tarifPerUnit = $tarifData ? (float) $tarifData['tarif'] : 0;
            $amount = $volume * $tarifPerUnit;
            $totalAmount += $amount;

            $this->itemModel->insert([
                'id_transaksi' => $idTransaksi,
                'id_jenis'     => $idJenis,
                'volume'       => $volume,
                'amount'       => $amount,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('notif_gagal', 'Gagal menyimpan transaksi.');
        }

        $message = $totalAmount == 0
            ? 'Transaksi berhasil disimpan dengan nilai nol. Silahkan lakukan pengecekan tarif.'
            : 'Transaksi berhasil disimpan.';

        return redirect()->to('/eretribusi/konfirmasi/' . $invoice)->with('notif_sukses', $message);
    }

    /**
     * Laporan Pendapatan per Unit (Puskesmas)
     */
    public function laporan()
    {
        if (session()->get('role') !== 'admin_kabupaten') {
            $idPuskesmas = session()->get('id_puskesmas');
        } else {
            $idPuskesmas = $this->request->getGet('id_puskesmas');
        }

        $query = $this->transaksiModel
            ->select('transaksi_retribusi.*, pasien.nama_pasien, pasien.no_rm')
            ->join('pasien', 'pasien.id = transaksi_retribusi.id_pasien', 'left');

        if ($idPuskesmas) {
            $query->where('id_puskesmas', $idPuskesmas);
        }

        $laporanRaw = $query->orderBy('invoice_date', 'DESC')->findAll();

        $laporan = [];
        foreach ($laporanRaw as $row) {
            $items = $this->itemModel->getItemsByTransaksi($row['id']);
            $totalAmount = 0;
            $itemNames = [];
            foreach ($items as $item) {
                $totalAmount += $item['amount'];
                $itemNames[] = $item['jenis'];
            }
            $row['jenis'] = implode(', ', $itemNames);
            $row['amount'] = $totalAmount;
            $laporan[] = $row;
        }

        return view('eretribusi/transaksi/laporan', [
            'laporan' => $laporan,
            'idPuskesmas' => $idPuskesmas
        ]);
    }

    /**
     * Generate unique invoice number
     * Format: RET-PUSKESMASCODE-YYMMDD-XXXXX
     */
    private function generateInvoiceNumber(int $idPuskesmas): string
    {
        $puskesmas = $this->puskesmasModel->find($idPuskesmas);
        $kodePuskesmas = $puskesmas ? $puskesmas['kode_retribusi'] : '000';

        $datePart = date('ymd');
        $randomPart = mt_rand(10000, 99999);
        $invoice = "RET-{$kodePuskesmas}-{$datePart}-{$randomPart}";

        while ($this->transaksiModel->isInvoiceExists($invoice)) {
            $randomPart = mt_rand(10000, 99999);
            $invoice = "RET-{$kodePuskesmas}-{$datePart}-{$randomPart}";
        }

        return $invoice;
    }
}
