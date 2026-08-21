<?php

namespace App\Controllers\H2h;

use App\Controllers\BaseController;

/**
 * H2H Controller — Spesifikasi Teknis API V1.0 Bank Jateng
 *
 * Response codes (resmi Bank Jateng):
 *   00 = Success
 *   01 = Payment Number Not Exist
 *   02 = No Outstanding Bill Payment
 *   03 = Failed to Get Token / Invalid Token
 *
 * Field length limits (Bank Jateng):
 *   nama_pasien  : max 30 char
 *   alamat_pasien: max 30 char
 *   no_rm        : max 20 char (numeric only)
 *   max tagihan  : 7 item per inquiry
 */
class H2hController extends BaseController
{
    protected $db;
    protected $transaksiModel;
    protected $itemModel;
    protected $pasienModel;

    // --- Bank Jateng response code constants ---
    const RESP_SUCCESS           = '00';
    const RESP_NOT_EXIST         = '01';
    const RESP_NO_OUTSTANDING    = '02';
    const RESP_FAILED_TOKEN      = '03';

    // --- Bank Jateng field length limits ---
    const MAX_NAMA_PASIEN  = 30;
    const MAX_ALAMAT_PASIEN = 30;
    const MAX_NO_RM        = 20;
    const MAX_TAGIHAN_ITEMS = 7;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->transaksiModel = new \App\Models\TransaksiRetribusiModel();
        $this->itemModel = new \App\Models\TransaksiItemModel();
        $this->pasienModel = new \App\Models\PasienModel();
    }

    // ----------------------------------------------------------------
    // LOGGING
    // ----------------------------------------------------------------
    protected function logRequest(string $endpoint, ?string $requestData, ?string $responseData): void
    {
        $this->db->table('h2h_logs')->insert([
            'endpoint'   => $endpoint,
            'request'    => $requestData,
            'response'   => $responseData,
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // ----------------------------------------------------------------
    // HELPER — Truncate sesuai batas Bank Jateng
    // ----------------------------------------------------------------
    protected function truncate(string $value, int $limit): string
    {
        return mb_strimwidth($value, 0, $limit, '');
    }

    // ----------------------------------------------------------------
    // ENDPOINT 1 — Authorization (GET /h2h/auth)
    // ----------------------------------------------------------------
    public function auth()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        $rawInput = json_encode(['headers' => $this->request->headers()]);

        // Validate Basic Auth header
        if (empty($authHeader) || !preg_match('/^Basic\s+(.*)$/i', $authHeader, $matches)) {
            $res = ['resp_code' => self::RESP_FAILED_TOKEN, 'resp_desc' => 'Failed to Get Token'];
            $this->logRequest('AUTH', $rawInput, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        $decoded = base64_decode($matches[1]);
        if (!$decoded || strpos($decoded, ':') === false) {
            $res = ['resp_code' => self::RESP_FAILED_TOKEN, 'resp_desc' => 'Failed to Get Token'];
            $this->logRequest('AUTH', $rawInput, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        [$username, $password] = explode(':', $decoded, 2);

        $expectedUser = env('H2H_API_USER', 'bankjateng');
        $expectedPass = env('H2H_API_PASS', 'puskesmas123');

        if ($username !== $expectedUser || $password !== $expectedPass) {
            $res = ['resp_code' => self::RESP_FAILED_TOKEN, 'resp_desc' => 'Failed to Get Token'];
            $this->logRequest('AUTH', $rawInput, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        // Generate token (berlaku 1 jam sesuai spec)
        $token     = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->db->table('h2h_tokens')->insert([
            'token'      => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $res = [
            'x-api-key' => $token,
            'resp_code' => self::RESP_SUCCESS,
            'resp_desc' => 'Success'
        ];

        $this->logRequest('AUTH', $rawInput, json_encode($res));
        return $this->response->setJSON($res);
    }

    // ----------------------------------------------------------------
    // TOKEN VALIDATION
    // ----------------------------------------------------------------
    protected function validateToken(): bool
    {
        $apiKey = $this->request->getHeaderLine('x-api-key');
        if (empty($apiKey)) {
            return false;
        }

        $tokenData = $this->db->table('h2h_tokens')
            ->where('token', $apiKey)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get()
            ->getRow();

        return !empty($tokenData);
    }

    // ----------------------------------------------------------------
    // ENDPOINT 2 — Inquiry (POST /h2h/inquiry)
    //
    // Request body: { "no_rm": "2304002648" }
    //
    // Response code sesuai spec Bank Jateng:
    //   00 = data ditemukan (ada tagihan outstanding)
    //   01 = Payment Number Not Exist (no_rm tidak ditemukan)
    //   02 = No Outstanding Bill Payment (ada no_rm tapi tagihan sudah lunas)
    //   03 = Invalid or Expired Token
    // ----------------------------------------------------------------
    public function inquiry()
    {
        $rawBody = $this->request->getBody();
        $input   = json_decode($rawBody, true);

        // --- Token validation ---
        if (!$this->validateToken()) {
            $res = ['resp_code' => self::RESP_FAILED_TOKEN, 'resp_desc' => 'Invalid or Expired Token'];
            $this->logRequest('INQUIRY', $rawBody, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        $noRm = $input['no_rm'] ?? null;

        // --- Validasi no_rm: wajib numerik, max 20 karakter ---
        if (empty($noRm) || !is_string($noRm) || !preg_match('/^[0-9]+$/', $noRm) || mb_strlen($noRm) > self::MAX_NO_RM) {
            $res = ['resp_code' => self::RESP_NOT_EXIST, 'resp_desc' => 'Payment Number Not Exist'];
            $this->logRequest('INQUIRY', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // --- Cari transaksi outstanding (status = pending) ---
        $transaksiList = $this->db->table('transaksi_retribusi')
            ->where('no_dokumen', $noRm)
            ->where('status', 'pending')
            ->get()
            ->getResultArray();

        // Fallback: status numerik 0 (data lama)
        if (empty($transaksiList)) {
            $transaksiList = $this->db->table('transaksi_retribusi')
                ->where('no_dokumen', $noRm)
                ->where('status', 0)
                ->get()
                ->getResultArray();
        }

        // --- Jika tidak ada transaksi dengan no_rm ini sama sekali → 01 ---
        $anyTransaksi = $this->db->table('transaksi_retribusi')
            ->where('no_dokumen', $noRm)
            ->countAllResults();

        if (empty($transaksiList) && $anyTransaksi === 0) {
            $res = ['resp_code' => self::RESP_NOT_EXIST, 'resp_desc' => 'Payment Number Not Exist'];
            $this->logRequest('INQUIRY', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // --- Jika no_rm ada tapi tidak ada tagihan outstanding → 02 ---
        if (empty($transaksiList)) {
            $res = ['resp_code' => self::RESP_NO_OUTSTANDING, 'resp_desc' => 'No Outstanding Bill Payment'];
            $this->logRequest('INQUIRY', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        $master = $transaksiList[0];

        // --- Ambil data pasien dari tabel terpisah ---
        $pasien = $this->pasienModel->find($master['id_pasien'] ?? null);

        // --- Ambil item tagihan (maks 7 item sesuai spec) ---
        $transaksiIds = array_column($transaksiList, 'id');
        $items = $this->db->table('transaksi_item')
            ->select('transaksi_item.*, jenis_retribusi.jenis as nama_layanan')
            ->join('jenis_retribusi', 'jenis_retribusi.id = transaksi_item.id_jenis', 'left')
            ->whereIn('transaksi_item.id_transaksi', $transaksiIds)
            ->orderBy('transaksi_item.id', 'ASC')
            ->limit(self::MAX_TAGIHAN_ITEMS)
            ->get()
            ->getResultArray();

        // --- Jika tidak ada item → 02 ---
        if (empty($items)) {
            $res = ['resp_code' => self::RESP_NO_OUTSTANDING, 'resp_desc' => 'No Outstanding Bill Payment'];
            $this->logRequest('INQUIRY', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // --- Hitung total & susun tagihan[] ---
        $totalTagihan = 0;
        $tagihanList  = [];
        foreach ($items as $item) {
            $amount = (int)$item['amount'];
            $totalTagihan += $amount;

            $noKwitansi  = 'S' . str_pad($item['id'], 9, '0', STR_PAD_LEFT);
            $namaLayanan = $item['nama_layanan'] ?? 'Retribusi Layanan Kesehatan';

            $tagihanList[] = [
                'no_kwitansi'     => $noKwitansi,
                'nominal_tagihan' => (string)$amount,
                'Keterangan'      => $namaLayanan . ' (' . $noKwitansi . ')'
            ];
        }

        // --- Ambil kode puskesmas ---
        $kodePuskesmas = '330600101Z'; // default
        if (!empty($master['id_puskesmas'])) {
            $puskesmas = $this->db->table('puskesmas')
                ->where('id', $master['id_puskesmas'])
                ->get()
                ->getRowArray();
            if (!empty($puskesmas['kode_retribusi'])) {
                $kodePuskesmas = $puskesmas['kode_retribusi'];
            }
        }

        // --- Hitung usia dari tgl_lahir ---
        $usia = '';
        if (!empty($pasien['tgl_lahir']) && $pasien['tgl_lahir'] !== '0000-00-00') {
            $usia = (string)(date('Y') - (int)substr($pasien['tgl_lahir'], 0, 4));
        }

        // --- Susun response sesuai spec Bank Jateng ---
        // TRUNCATE field sesuai batas Bank Jateng (nama max 30, alamat max 30)
        $res = [
            'resp_code'      => self::RESP_SUCCESS,
            'resp_desc'      => 'data ditemukan',
            'no_rm'          => (string)$noRm,
            'kode_puskesmas' => $kodePuskesmas,
            'nama_pasien'    => $this->truncate($pasien['nama_pasien'] ?? '', self::MAX_NAMA_PASIEN),
            'alamat_pasien'  => $this->truncate($pasien['alamat_pasien'] ?? '', self::MAX_ALAMAT_PASIEN),
            'jenis_kelamin'  => $this->truncate($pasien['jenis_kelamin'] ?? '', 10),
            'usia'           => $usia,
            'tgl_lahir'      => $pasien['tgl_lahir'] ?? '',
            'total_tagihan'  => (string)$totalTagihan,
            'tagihan'        => $tagihanList
        ];

        $this->logRequest('INQUIRY', $rawBody, json_encode($res));
        return $this->response->setJSON($res);
    }

    // ----------------------------------------------------------------
    // ENDPOINT 3 — Payment (POST /h2h/payment)
    //
    // Request body: { "no_rm", "total_tagihan", "noreff", "channel", "device" }
    //
    // Response code sesuai spec Bank Jateng:
    //   00 = Success (pembayaran tercatat)
    //   01 = Payment Number Not Exist
    //   02 = No Outstanding Bill Payment (sudah lunas atau tidak ditemukan)
    //   03 = Invalid or Expired Token
    //
    // CATATAN: Bank Jateng TIDAK mendefinisikan kode 04 untuk Nominal Mismatch.
    // Jika nominal tidak sesuai, tetap kembalikan 02 dengan log tambahan di sisi server.
    // ----------------------------------------------------------------
    public function payment()
    {
        $rawBody = $this->request->getBody();
        $input   = json_decode($rawBody, true);

        // --- Token validation ---
        if (!$this->validateToken()) {
            $res = ['resp_code' => self::RESP_FAILED_TOKEN, 'resp_desc' => 'Invalid or Expired Token'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        $noRm         = $input['no_rm'] ?? null;
        $totalTagihan = (int)($input['total_tagihan'] ?? 0);
        $noreff       = $input['noreff'] ?? null;

        // --- Validasi no_rm ---
        if (empty($noRm) || !is_string($noRm) || !preg_match('/^[0-9]+$/', $noRm) || mb_strlen($noRm) > self::MAX_NO_RM) {
            $res = ['resp_code' => self::RESP_NOT_EXIST, 'resp_desc' => 'Payment Number Not Exist'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // --- Validasi noreff ---
        if (empty($noreff)) {
            $res = ['resp_code' => self::RESP_NO_OUTSTANDING, 'resp_desc' => 'No Outstanding Bill Payment'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // --- IDEMPOTENCY CHECK: Jika noreff sudah pernah diproses → return sukses ---
        $existingPayment = $this->db->table('transaksi_retribusi')
            ->where('noreff_bank', $noreff)
            ->get()
            ->getRowArray();

        if (!empty($existingPayment)) {
            $res = [
                'resp_code' => self::RESP_SUCCESS,
                'resp_desc' => 'Success',
                'no_reff'   => 'RV-' . $existingPayment['id']
            ];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // --- Cari transaksi outstanding ---
        $transaksiList = $this->db->table('transaksi_retribusi')
            ->where('no_dokumen', $noRm)
            ->where('status', 'pending')
            ->get()
            ->getResultArray();

        if (empty($transaksiList)) {
            $transaksiList = $this->db->table('transaksi_retribusi')
                ->where('no_dokumen', $noRm)
                ->where('status', 0)
                ->get()
                ->getResultArray();
        }

        // --- Jika tidak ada transaksi → 02 ---
        if (empty($transaksiList)) {
            $res = ['resp_code' => self::RESP_NO_OUTSTANDING, 'resp_desc' => 'No Outstanding Bill Payment'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // --- Hitung total dari database ---
        $transaksiIds = array_column($transaksiList, 'id');
        $items = $this->db->table('transaksi_item')
            ->whereIn('id_transaksi', $transaksiIds)
            ->get()
            ->getResultArray();

        $calcTotal = (int)array_sum(array_column($items, 'amount'));

        // --- Validasi nominal ---
        // CATATAN: Bank Jateng tidak punya kode 04 untuk mismatch.
        // Jika nominal tidak sesuai → kembalikan 02 + log detail di server.
        // Rekomendasi: ajukan kode 04 ke Bank Jateng.
        if ($calcTotal !== $totalTagihan) {
            $res = ['resp_code' => self::RESP_NO_OUTSTANDING, 'resp_desc' => 'No Outstanding Bill Payment'];
            // Log detail di server untuk debugging (tidak dikirim ke Bank)
            $this->logRequest('PAYMENT-NOMINAL-MISMATCH', $rawBody, json_encode([
                'resp_code'          => self::RESP_NO_OUTSTANDING,
                'resp_desc'          => 'No Outstanding Bill Payment',
                'detail'             => 'Nominal tidak sesuai: DB=' . $calcTotal . ', Request=' . $totalTagihan,
                'rekomendasi'        => 'Ajukan kode 04 Nominal Mismatch ke Bank Jateng'
            ]));
            return $this->response->setJSON($res);
        }

        // --- UPDATE STATUS → PAID ---
        $this->db->transStart();

        $this->db->table('transaksi_retribusi')
            ->whereIn('id', $transaksiIds)
            ->update([
                'status'      => 'paid',
                'noreff_bank' => $noreff,
                'channel'     => $input['channel'] ?? '',
                'device'      => $input['device'] ?? '',
                'updated_at'  => date('Y-m-d H:i:s')
            ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $res = ['resp_code' => self::RESP_NO_OUTSTANDING, 'resp_desc' => 'No Outstanding Bill Payment'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // --- Response sukses ---
        $res = [
            'resp_code' => self::RESP_SUCCESS,
            'resp_desc' => 'Success',
            'no_reff'   => 'RV-' . $transaksiList[0]['id']
        ];

        $this->logRequest('PAYMENT', $rawBody, json_encode($res));
        return $this->response->setJSON($res);
    }
}
