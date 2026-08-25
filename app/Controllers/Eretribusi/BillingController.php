<?php

namespace App\Controllers\Eretribusi;

use App\Controllers\BaseController;
use App\Models\BillModel;
use App\Models\PuskesmasModel;
use App\Models\TransaksiRetribusiModel;
use App\Services\Billing\BillingService;
use App\Services\BimaQRService;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\TransaksiItemModel;


class BillingController extends BaseController
{
    protected $billingService;
    protected $transaksiModel;
    protected $billModel;
    protected $puskesmasModel;
    protected $transaksiItem;

    public function __construct()
    {
        $this->billingService = new BillingService();
        $this->transaksiModel = new TransaksiRetribusiModel();
        $this->transaksiItem = new TransaksiItemModel();
        $this->billModel      = new BillModel();
        $this->puskesmasModel = new PuskesmasModel();
    }

    /**
     * Tampilkan halaman konfirmasi pembayaran
     */
    public function konfirmasi(string $invoice)
    {
        $transaksi = $this->transaksiModel->getByInvoice($invoice);

        if (empty($transaksi)) {
            return redirect()->back()->with('notif_gagal', 'Data transaksi tidak ditemukan.');
        }

        // Tenant Isolation: Cek apakah user punya akses ke puskesmas ini
        if (session()->get('role') !== 'admin_kabupaten' && session()->get('id_puskesmas') != $transaksi['id_puskesmas']) {
            return redirect()->to('/')->with('notif_gagal', 'Anda tidak memiliki hak akses ke data transaksi puskesmas lain.');
        }

        // Ambil data puskesmas untuk keterangan billing
        $idPuskesmas = $transaksi['id_puskesmas'];
        $puskesmas   = $this->puskesmasModel->find($idPuskesmas);

        // Load Items
        $items = (new \App\Models\TransaksiItemModel())->getItemsByTransaksi($transaksi['id']);

        return view('eretribusi/konfirmasi_pembayaran', [
            'transaksi_master' => $transaksi,
            'items' => $items,
            'puskesmas' => $puskesmas,
            'invoice'   => $invoice
        ]);
    }

    /**
     * Proses generate ID Billing via API
     */
    public function generate()
    {
        $invoice = $this->request->getPost('invoice');

        // 1. Ambil data transaksi
        $transaksi = $this->transaksiModel->getByInvoice($invoice);
        if (empty($transaksi)) {
            return redirect()->back()->with('notif_gagal', 'Transaksi tidak valid.');
        }

        // 2. Ambil items dan hitung total nominal
        $items = (new \App\Models\TransaksiItemModel())->getItemsByTransaksi($transaksi['id']);
        $totalNominal = 0;
        foreach ($items as $item) {
            $totalNominal += $item['amount'];
        }

        if ($totalNominal <= 0) {
            return redirect()->back()->with('notif_gagal', 'Total bayar tidak boleh nol.');
        }

        // 3. Ambil data puskesmas
        $idPuskesmas = $transaksi['id_puskesmas'];
        $puskesmas   = $this->puskesmasModel->find($idPuskesmas);

        // 4. Request ke Billing Server
        $billingData = [
            'kode_retribusi' => $puskesmas['kode_retribusi'],
            'nominal'        => $totalNominal,
            'keterangan'     => $transaksi['no_dokumen'] . '-' . $puskesmas['prasarana'],
            'no_dokumen'     => $transaksi['invoice'],
        ];

        $response = $this->billingService->generateIdBilling($billingData);

        if (!$response) {
            return redirect()->back()->with('notif_gagal', 'Server billing tidak merespon. Coba beberapa saat lagi..!');
        }

        $idBilling = $response['IdBilling'];

        // 5. Cek duplikasi di DB
        if ($this->billModel->existsByIdBilling($idBilling)) {
            return redirect()->back()->with('notif_gagal', 'ID Billing sudah pernah di-generate untuk transaksi ini.');
        }

        // 6. Simpan ke database (Atomic Transaction)
        $db = \Config\Database::connect();
        $db->transStart();

        // Simpan master billing
        $this->billModel->insert(['id_billing' => $idBilling]);

        // Update ID Billing di semua baris transaksi terkait
        $this->transaksiModel->where('invoice', $invoice)
                            ->set(['id_billing' => $idBilling])
                            ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('notif_gagal', 'Gagal menyimpan data billing ke database.');
        }

        return redirect()->to("eretribusi/qris/$idBilling");
    }

    /**
     * Tampilkan halaman cek status billing
     */
    public function cekStatus()
    {
        return view('eretribusi/cek_status');
    }

    /**
     * Cek status transaksi berdasarkan No. RM (tanpa ID Billing / H2H status)
     */
    public function cekStatusByNoRm()
    {
        $noRm = $this->request->getPost('no_rm');

        if (empty($noRm) || !preg_match('/^[0-9]+$/', $noRm) || mb_strlen($noRm) > 20) {
            return redirect()->back()->with('notif_gagal', 'No. RM tidak valid.');
        }

        // Cari transaksi terakhir berdasarkan No. RM (tanpa filter status dulu, lihat semua)
        $transaksi = $this->transaksiModel
            ->select('transaksi_retribusi.*, puskesmas.kode_retribusi, puskesmas.prasarana')
            ->join('puskesmas', 'puskesmas.id = transaksi_retribusi.id_puskesmas', 'left')
            ->where('no_dokumen', $noRm)
            ->orderBy('id', 'DESC')
            ->first();
        // History transaksi sebelumnya (kecuali yang terbaru)
        $history = $this->transaksiModel
            ->select('transaksi_retribusi.*, puskesmas.kode_retribusi, puskesmas.prasarana')
            ->join('puskesmas', 'puskesmas.id = transaksi_retribusi.id_puskesmas', 'left')
            ->where('no_dokumen', $noRm)
            ->orderBy('id', 'DESC')
            ->findAll();
        // Remove transaksi terbaru dari riwayat
        if (!empty($history)) {
            array_shift($history);
        }

        if (empty($transaksi)) {
            return view('eretribusi/cek_status_result', [
                'status' => null,
                'error'  => 'Tidak ada transaksi untuk No. RM ini.'
            ]);
        }

        // Hitung nominal total dari items
        $transaksiId = $transaksi['id'];

        $items = $this->transaksiItem->select('SUM(amount) as total')
            ->where('id_transaksi', $transaksiId)
            ->first();
        $nominal = (int)($items['total'] ?? 0);

        // Ambil rincian item untuk detail
        $itemsDetail = $this->transaksiItem->getItemsByTransaksi($transaksiId);

        // Cek status transaksi di DB
        $dbStatus = strtolower($transaksi['status'] ?? '');

        // Tentukan status untuk view
        $isLunas = ($dbStatus === 'paid' || $dbStatus === 'lunas');
        $statusString = $isLunas ? 'LUNAS' : 'BELUM LUNAS';
        $tglBayar = $isLunas ? date('Y-m-d H:i:s') : null;

        // Susun response sesuai format yang view ekspektasi (mirip billing service)
        $res = [
            'IdBilling' => $transaksi['id_billing'] ?? '',
            'NoDokumen' => $transaksi['no_dokumen'],
            'Nominal'   => $nominal,
            'Status'    => $statusString,
            'TglBayar'  => $tglBayar
        ];

        return view('eretribusi/cek_status_result', [
            'status' => $res,
            'id_billing' => $res['IdBilling'],
            'no_rm'    => $noRm,
            'items_detail' => $itemsDetail ?? [],
            'history'  => $history ?? []
        ]);
    }

    /**
     * Proses pengecekan status ke Billing Server
     */
    public function prosesCekStatus()
    {
        $idBilling = $this->request->getPost('id_billing');

        if (empty($idBilling)) {
            return redirect()->back()->with('notif_gagal', 'ID Billing harus diisi.');
        }

        $status = $this->billingService->cekStatusPembayaran($idBilling);

        if (!$status) {
            return redirect()->back()->with('notif_gagal', 'Gagal mengambil data dari server billing.');
        }

        // Jika status sudah LUNAS, update di database lokal jika perlu
        if ($status['Status'] === 'LUNAS') {
            $this->transaksiModel->where('id_billing', $idBilling)
                                ->set(['status' => 'lunas'])
                                ->update();
        }

        return view('eretribusi/cek_status_result', [
            'status' => $status,
            'id_billing' => $idBilling
        ]);
    }

    /**
     * Tampilkan halaman QRIS
     */
    /**
     * API: Ambil detail transaksi (JSON)
     */
    public function getDetail(string $invoice)
    {
        $transaksi = $this->transaksiModel
            ->select('transaksi_retribusi.*, puskesmas.prasarana')
            ->join('puskesmas', 'puskesmas.id = transaksi_retribusi.id_puskesmas', 'left')
            ->where('invoice', $invoice)
            ->first();

        if (!$transaksi) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Transaksi tidak ditemukan']);
        }

        $items = $this->transaksiItem->getItemsByTransaksi($transaksi['id']);

        $total = 0;
        foreach($items as $it) {
            $total += $it['amount'];
        }
        $transaksi['amount'] = $total;
        $transaksi['items_detail'] = $items;

        return $this->response->setJSON($transaksi);
    }
}
