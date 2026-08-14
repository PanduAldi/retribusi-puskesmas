<?php

namespace App\Controllers\H2h;

use App\Controllers\BaseController;

class H2hController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    protected function logRequest(string $endpoint, ?string $requestData, ?string $responseData)
    {
        $this->db->table('h2h_logs')->insert([
            'endpoint'   => $endpoint,
            'request'    => $requestData,
            'response'   => $responseData,
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 1. Authorization Endpoint (GET /h2h/auth)
     */
    public function auth()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        $rawInput = json_encode(['headers' => $this->request->headers()]);

        if (empty($authHeader) || !preg_match('/^Basic\s+(.*)$/i', $authHeader, $matches)) {
            $res = ['resp_code' => '03', 'resp_desc' => 'Failed to Get Token'];
            $this->logRequest('AUTH', $rawInput, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        $decoded = base64_decode($matches[1]);
        if (!$decoded || strpos($decoded, ':') === false) {
            $res = ['resp_code' => '03', 'resp_desc' => 'Failed to Get Token'];
            $this->logRequest('AUTH', $rawInput, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        [$username, $password] = explode(':', $decoded, 2);

        $expectedUser = env('H2H_API_USER', 'bankjateng');
        $expectedPass = env('H2H_API_PASS', 'puskesmas123');

        if ($username !== $expectedUser || $password !== $expectedPass) {
            $res = ['resp_code' => '03', 'resp_desc' => 'Failed to Get Token'];
            $this->logRequest('AUTH', $rawInput, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->db->table('h2h_tokens')->insert([
            'token'      => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $res = [
            'x-api-key' => $token,
            'resp_code' => '00',
            'resp_desc' => 'Success'
        ];

        $this->logRequest('AUTH', $rawInput, json_encode($res));
        return $this->response->setJSON($res);
    }

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

    /**
     * 2. Inquiry Endpoint (POST /h2h/inquiry)
     */
    public function inquiry()
    {
        $rawBody = $this->request->getBody();
        $input = json_decode($rawBody, true);

        if (!$this->validateToken()) {
            $res = ['resp_code' => '03', 'resp_desc' => 'Invalid or Expired Token'];
            $this->logRequest('INQUIRY', $rawBody, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        $noRm = $input['no_rm'] ?? null;

        // Validasi no_rm wajib numerik sesuai spesifikasi Bank Jateng agar bisa dibayar via ATM
        if (empty($noRm) || !preg_match('/^[0-9]+$/', (string)$noRm)) {
            $res = ['resp_code' => '01', 'resp_desc' => 'Payment Number Not Exist'];
            $this->logRequest('INQUIRY', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // Cari transaksi berdasarkan no_dokumen (no_rm) yang statusnya belum lunas ('pending' atau '0')
        $transaksi = $this->db->table('transaksi_retribusi')
            ->where('no_dokumen', $noRm)
            ->where('status', 'pending')
            ->get()
            ->getResultArray();

        if (empty($transaksi)) {
            // Coba cek dengan status '0' (jika ada data lama)
            $transaksi = $this->db->table('transaksi_retribusi')
                ->where('no_dokumen', $noRm)
                ->where('status', 0)
                ->get()
                ->getResultArray();
        }

        if (empty($transaksi)) {
            $res = ['resp_code' => '01', 'resp_desc' => 'Payment Number Not Exist'];
            $this->logRequest('INQUIRY', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        $master = $transaksi[0];
        $puskesmas = $this->db->table('puskesmas')->where('id', $master['id_puskesmas'])->get()->getRowArray();

        // Ambil item tagihan dari tabel transaksi_item dengan join ke jenis_retribusi untuk mendapatkan nama layanan
        $transaksiIds = array_column($transaksi, 'id');
        $items = $this->db->table('transaksi_item')
            ->select('transaksi_item.*, jenis_retribusi.jenis as nama_layanan')
            ->join('jenis_retribusi', 'jenis_retribusi.id = transaksi_item.id_jenis', 'left')
            ->whereIn('transaksi_item.id_transaksi', $transaksiIds)
            ->limit(7)
            ->get()
            ->getResultArray();

        if (empty($items)) {
            $res = ['resp_code' => '02', 'resp_desc' => 'No Outstanding Bill Payment'];
            $this->logRequest('INQUIRY', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        $totalTagihan = 0;
        $tagihanList = [];
        foreach ($items as $item) {
            $amount = (int)$item['amount'];
            $totalTagihan += $amount;

            // Format no_kwitansi sesuai standar Bank Jateng (S + 9 digit)
            $noKwitansi = 'S' . str_pad($item['id'], 9, '0', STR_PAD_LEFT);
            $namaLayanan = $item['nama_layanan'] ?? 'Retribusi Layanan Kesehatan';

            $tagihanList[] = [
                'no_kwitansi'     => $noKwitansi,
                'nominal_tagihan' => (string)$amount,
                'Keterangan'      => $namaLayanan . ' (' . ($master['invoice'] ?? 'INV') . ')'
            ];
        }

        // Kode Puskesmas mengikuti data master puskesmas (kode_retribusi) atau default daerah
        $kodePuskesmas = !empty($puskesmas['kode_retribusi']) ? $puskesmas['kode_retribusi'] : '330600101Z';

        // Usia dihitung otomatis dari tanggal lahir (jika tersedia)
        $usia = null;
        if (!empty($master['tgl_lahir']) && $master['tgl_lahir'] !== '0000-00-00') {
            $usia = (string)(date('Y') - (int)substr($master['tgl_lahir'], 0, 4));
        }

        $res = [
            'resp_code'      => '00',
            'resp_desc'      => 'data ditemukan',
            'no_rm'          => (string)$noRm,
            'kode_puskesmas' => $kodePuskesmas,
            'nama_pasien'    => $master['nama_pasien'] ?? 'PASIEN ' . $noRm,
            'alamat_pasien'  => $master['alamat_pasien'] ?? '-',
            'jenis_kelamin'  => $master['jenis_kelamin'] ?? '-',
            'usia'           => $usia ?? '',
            'tgl_lahir'      => $master['tgl_lahir'] ?? '',
            'total_tagihan'  => (string)$totalTagihan,
            'tagihan'        => $tagihanList
        ];

        $this->logRequest('INQUIRY', $rawBody, json_encode($res));
        return $this->response->setJSON($res);
    }

    /**
     * 3. Payment / Posting Endpoint (POST /h2h/payment)
     */
    public function payment()
    {
        $rawBody = $this->request->getBody();
        $input = json_decode($rawBody, true);

        if (!$this->validateToken()) {
            $res = ['resp_code' => '03', 'resp_desc' => 'Invalid or Expired Token'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setStatusCode(401)->setJSON($res);
        }

        $noRm         = $input['no_rm'] ?? null;
        $totalTagihan = (int)($input['total_tagihan'] ?? 0);
        $noreff       = $input['noreff'] ?? null;

        if (empty($noRm) || empty($noreff)) {
            $res = ['resp_code' => '02', 'resp_desc' => 'No Outstanding Bill Payment'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // Idempotency check: Cek apakah noreff bank sudah pernah diproses
        $existingPayment = $this->db->table('transaksi_retribusi')
            ->where('noreff_bank', $noreff)
            ->get()
            ->getRowArray();

        if (!empty($existingPayment)) {
            $res = [
                'resp_code' => '00',
                'resp_desc' => 'Success',
                'no_reff'   => 'RV-' . $existingPayment['id']
            ];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // Cari transaksi outstanding
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

        if (empty($transaksiList)) {
            $res = ['resp_code' => '02', 'resp_desc' => 'No Outstanding Bill Payment'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        $transaksiIds = array_column($transaksiList, 'id');
        $items = $this->db->table('transaksi_item')
            ->whereIn('id_transaksi', $transaksiIds)
            ->get()
            ->getResultArray();

        $calcTotal = array_sum(array_column($items, 'amount'));
        if ($calcTotal !== $totalTagihan) {
            $res = ['resp_code' => '02', 'resp_desc' => 'Nominal Mismatch'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        // Atomically update status to lunas
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
            $res = ['resp_code' => '02', 'resp_desc' => 'Database Transaction Failed'];
            $this->logRequest('PAYMENT', $rawBody, json_encode($res));
            return $this->response->setJSON($res);
        }

        $res = [
            'resp_code' => '00',
            'resp_desc' => 'Success',
            'no_reff'   => 'RV-' . $transaksiList[0]['id']
        ];

        $this->logRequest('PAYMENT', $rawBody, json_encode($res));
        return $this->response->setJSON($res);
    }
}
