<?php

namespace App\Controllers\Eretribusi;

use App\Controllers\BaseController;
use App\Models\TransaksiRetribusiModel;
use App\Models\TarifRetribusiModel;
use App\Models\JenisRetribusiModel;
use App\Models\PuskesmasModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class TransaksiController extends BaseController
{
    protected $transaksiModel;
    protected $tarifModel;
    protected $jenisModel;
    protected $puskesmasModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiRetribusiModel();
        $this->tarifModel = new TarifRetribusiModel();
        $this->jenisModel = new JenisRetribusiModel();
        $this->puskesmasModel = new PuskesmasModel();
    }

    /**
     * List transactions for current puskesmas
     */
    public function index()
    {
        // Tenant Isolation: Hanya menampilkan transaksi puskesmas saat ini
        if (session()->get('role') !== 'admin_kabupaten') {
            $idPuskesmas = session()->get('id_puskesmas');
        } else {
            // Admin kabupaten bisa melihat semua transaksi
            $idPuskesmas = null;
        }

        if ($idPuskesmas) {
            $transaksi = $this->transaksiModel
                ->select('transaksi_retribusi.*, jenis_retribusi.jenis, puskesmas.prasarana')
                ->join('jenis_retribusi', 'jenis_retribusi.id = transaksi_retribusi.id_jenis')
                ->join('puskesmas', 'puskesmas.id = transaksi_retribusi.id_puskesmas')
                ->where('transaksi_retribusi.id_puskesmas', $idPuskesmas)
                ->orderBy('transaksi_retribusi.invoice_date', 'DESC')
                ->findAll();
        } else {
            // Admin kabupaten melihat semua transaksi
            $transaksi = $this->transaksiModel
                ->select('transaksi_retribusi.*, jenis_retribusi.jenis, puskesmas.prasarana')
                ->join('jenis_retribusi', 'jenis_retribusi.id = transaksi_retribusi.id_jenis')
                ->join('puskesmas', 'puskesmas.id = transaksi_retribusi.id_puskesmas')
                ->orderBy('transaksi_retribusi.invoice_date', 'DESC')
                ->findAll();
        }

        return view('eretribusi/transaksi/index', [
            'transaksi' => $transaksi
        ]);
    }

    /**
     * Show form to input transaction
     * Needs to load tarif for current puskesmas
     */
    public function create()
    {
        // Get current puskesmas (except for admin kabupaten)
        if (session()->get('role') !== 'admin_kabupaten') {
            $idPuskesmas = session()->get('id_puskesmas');
        } else {
            // Admin kabupaten perlu memilih puskesmas
            $idPuskesmas = $this->request->getGet('id_puskesmas');
            if (!$idPuskesmas) {
                return redirect()->to('/eretribusi/transaksi')->with('notif_gagal', 'Pilih puskesmas terlebih dahulu.');
            }
        }

        // Load tarif for current puskesmas
        $tarif = [];
        if ($idPuskesmas) {
            $tarif = $this->tarifModel->getTarifByPuskesmas($idPuskesmas);
        }

        // Load jenis retribusi for dropdown
        $jenis = $this->jenisModel->findAll();

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
            'tarif' => $tarif,
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
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'id_jenis' => 'required|numeric',
            'volume' => 'required|numeric|greater_than[0]',
            // Amount validation will be custom since it depends on tarif * volume
        ]);

        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Get input data
        $idPuskesmas = session()->get('role') !== 'admin_kabupaten'
            ? session()->get('id_puskesmas')
            : $this->request->getPost('id_puskesmas');

        $idJenis = $this->request->getPost('id_jenis');
        $volume = (float) $this->request->getPost('volume');

        // Get tarif for calculation
        $tarifData = $this->tarifModel->where('id_puskesmas', $idPuskesmas)
                                     ->where('id_jenis', $idJenis)
                                     ->first();

        if (!$tarifData) {
            return redirect()->back()->withInput()->with('notif_gagal', 'Tarif tidak ditemukan untuk jenis retribusi dan puskesmas tersebut.');
        }

        $tarifPerUnit = (float) $tarifData['tarif'];
        $amount = $volume * $tarifPerUnit;

        // Handle amount = 0 rule: if amount is 0, still allow but with special handling
        // According to requirement, we need to handle amount = 0 rule
        // We'll allow it but might need special processing later

        // Generate unique invoice number
        $invoice = $this->generateInvoiceNumber($idPuskesmas);

        // Prepare data for insertion
        $data = [
            'id_puskesmas' => $idPuskesmas,
            'id_jenis' => $idJenis,
            'invoice' => $invoice,
            'invoice_date' => date('Y-m-d'),
            'volume' => $volume,
            'amount' => $amount,
            'status' => 'pending' // Default status
        ];

        // Save to database
        $db = \Config\Database::connect();
        $db->transStart();

        $insertId = $this->transaksiModel->insert($data);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('notif_gagal', 'Gagal menyimpan transaksi.');
        }

        $message = $amount == 0
            ? 'Transaksi berhasil disimpan dengan nilai nol. Silahkan lakukan pengecekan tarif.'
            : 'Transaksi berhasil disimpan.';

        // Redirect ke halaman konfirmasi pembayaran
        return redirect()->to('/eretribusi/konfirmasi/' . $invoice)->with('notif_sukses', $message);
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