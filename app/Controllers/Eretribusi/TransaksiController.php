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
        // Tenant Isolation: Admin Puskesmas hanya melihat unitnya sendiri
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

        $transaksiRaw = $query->orderBy('invoice_date', 'DESC')->findAll();

        $totalTerbayar = 0;
        $totalBelumTerbayar = 0;
        $totalTransaksi = count($transaksiRaw);

        $transaksi = [];
        foreach ($transaksiRaw as $row) {
            $items = $this->itemModel->getItemsByTransaksi($row['id']);

            $totalAmount = 0;
            $itemNames = [];
            foreach ($items as $item) {
                $totalAmount += $item['amount'];
                $itemNames[] = $item['jenis'];
            }
            $row['items_detail'] = $items;
            $row['jenis'] = implode(', ', $itemNames);
            $row['amount'] = $totalAmount;
            $row['volume'] = count($items);
            $transaksi[] = $row;

            if ($row['status'] == 'paid') {
                $totalTerbayar += $totalAmount;
            } else {
                $totalBelumTerbayar += $totalAmount;
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
        // Tenant Isolation: Admin Puskesmas hanya melihat unitnya sendiri
        if (session()->get('role') !== 'admin_kabupaten') {
            $idPuskesmas = session()->get('id_puskesmas');
        } else {
            $idPuskesmas = $this->request->getGet('id_puskesmas');
            if (!$idPuskesmas) {
                return redirect()->to('/eretribusi/transaksi')->with('notif_gagal', 'Pilih puskesmas terlebih dahulu.');
            }
        }

        // Load jenis retribusi for dropdown (as tarif) - Filter by Puskesmas Mapping
        $allowedJenisIds = $this->puskesmasJenisModel->getJenisIdsByPuskesmas($idPuskesmas);
        if (empty($allowedJenisIds)) {
            $jenis = [];
        } else {
            $jenis = $this->jenisModel->whereIn('id', $allowedJenisIds)->findAll();
        }

        // Load puskesmas data (for admin kabupaten selection)
        $puskesmas = [];
        if (session()->get('role') === 'admin_kabupaten') {
            $puskesmas = $this->puskesmasModel->findAll();
        }

        // Load current puskesmas data for display
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
     * Save transaction to DB
     * Generate unique invoice number
     * Handle amount = 0 rule
     */
    public function store()
    {
        // Validation
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

        // Get input data
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

        // Prepare data for insertion
        $dataTransaksi = [
            'id_puskesmas'  => $idPuskesmas,
            'id_pasien'     => $idPasien,
            'no_dokumen'    => $noDokumen,
            'invoice'       => $invoice,
            'invoice_date'  => date('Y-m-d'),
            'status'        => 'pending'
        ];

        // Save to database
        $db = \Config\Database::connect();
        $db->transStart();

        $idTransaksi = $this->transaksiModel->insert($dataTransaksi);

        $totalAmount = 0;
        foreach ($idJenisArr as $key => $idJenis) {
            $volume = (float) $volumeArr[$key];

            // Validation: Ensure the service is allowed for this Puskesmas
            if (!$this->puskesmasJenisModel->isAllowed($idPuskesmas, (int)$idJenis)) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('notif_gagal', 'Terdapat jenis layanan yang tidak diizinkan untuk Puskesmas ini.');
            }

            // Get tarif for calculation from jenisModel
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

        // Redirect ke halaman konfirmasi pembayaran
        return redirect()->to('/eretribusi/konfirmasi/' . $invoice)->with('notif_sukses', $message);
    }

    /**
     * Laporan Pendapatan per Unit (Puskesmas)
     */
    public function laporan()
    {
        // Tenant Isolation: Admin Puskesmas hanya melihat unitnya sendiri
        if (session()->get('role') !== 'admin_kabupaten') {
            $idPuskesmas = session()->get('id_puskesmas');
        } else {
            $idPuskesmas = $this->request->getGet('id_puskesmas');
        }

        $query = $this->transaksiModel->select('transaksi_retribusi.*, pasien.nama_pasien, pasien.no_rm')
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
        // Get puskesmas code
        $puskesmas = $this->puskesmasModel->find($idPuskesmas);
        $kodePuskesmas = $puskesmas ? $puskesmas['kode_retribusi'] : '000';

        // Date part: YYMMDD
        $datePart = date('ymd');

        // Generate random 5-digit number
        $randomPart = mt_rand(10000, 99999);

        // Base invoice format
        $invoice = "RET-{$kodePuskesmas}-{$datePart}-{$randomPart}";

        // Ensure uniqueness
        while ($this->transaksiModel->isInvoiceExists($invoice)) {
            $randomPart = mt_rand(10000, 99999);
            $invoice = "RET-{$kodePuskesmas}-{$datePart}-{$randomPart}";
        }

        return $invoice;
    }
}