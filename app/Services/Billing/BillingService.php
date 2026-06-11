<?php

namespace App\Services\Billing;

/**
 * BillingService handles integration with the External Billing Server API.
 */
class BillingService
{
    protected $apiUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        // Credentials should be in .env
        $this->apiUrl   = env('BILLING_URL', 'http://15.0.4.27:8080/interface/create/');
        $this->username = env('BILLING_USERNAME', 'peternakan'); // Default from context for initial setup
        $this->password = env('BILLING_PASSWORD', 'dQ');        // Default from context for initial setup
    }

    /**
     * Generate ID Billing from External Server
     *
     * @param array $data Input data for billing generation
     * @return array|null Response containing IdBilling or null on failure
     */
    public function generateIdBilling(array $data): ?array
    {
        // Configurable retry count (default 3) and simple exponential back‑off (seconds)
        $maxAttempts = (int) env('BILLING_MAX_RETRY', 3);
        $attempt = 0;

        // Payload is built once – it does not change between retries
        $payload = [
            'Username'      => $this->username,
            'Password'      => $this->password,
            'KodeRetribusi' => $data['kode_retribusi'],
            'Nominal'       => (int) $data['nominal'],
            'Keterangan'    => $data['keterangan'],
            'NoDokumen'     => $data['no_dokumen'],
            'TglPenetapan'  => $data['tgl_penetapan'] ?? date('Ymd'),
            'TglJthTempo'   => $data['tgl_jatuh_tempo'] ?? date('Ymd'),
            'Commit'        => 'True',
        ];

        while ($attempt < $maxAttempts) {
            $attempt++;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Internal network – keep verification disabled as per existing setup
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            // Timeout handling
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $result = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($result['IdBilling'])) {
                    // Success – exit retry loop
                    return $result;
                }
                // Invalid JSON or missing IdBilling – treat as failure
                log_message('error', "Billing API invalid response on attempt {$attempt}: " . $response);
            } else {
                // Network / HTTP error
                log_message('error', "Billing API failure on attempt {$attempt}: HTTP {$httpCode} - {$error}");
            }

            // If we have more attempts left, wait using exponential back‑off
            if ($attempt < $maxAttempts) {
                $sleep = (int) pow(2, $attempt); // 2, 4, 8 seconds …
                usleep($sleep * 1000000); // usleep expects microseconds
            }
        }

        // All attempts exhausted.
        // In local development we can return a mock object so the UI can continue testing.
        if (env('CI_ENVIRONMENT') === 'development') {
            return [
                'IdBilling' => 'MOCK-' . uniqid('', true),
                'NoDokumen'=> $data['no_dokumen'],
                'Nominal'  => $data['nominal'],
                'Status'   => 'Pending',
                'TglBayar'=> null,
            ];
        }

        return null;
    }

    /**
     * Cek status pembayaran ID Billing dari External Server
     *
     * @param string $idBilling ID Billing
     * @return array|null Response status pembayaran atau null on failure
     */
    public function  cekStatusPembayaran(string $idBilling): ?array
    {
        // URL Cek Status biasanya berbeda dengan create billing, kita sesuaikan dengan pola API yang sama.
        // Di sini kita asumsikan URL cek status adalah di '/interface/check/' atau parameter check.
        // Kita juga bisa menggunakan URL yang didefinisikan secara khusus, atau fallback.
        $checkUrl = str_replace('/create/', '/check/', $this->apiUrl);

        $payload = [
            'Username'  => $this->username,
            'Password'  => $this->password,
            'IdBilling' => $idBilling,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $checkUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Security Exception for Internal Network
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Timeout handling
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            log_message('error', 'Billing API Check Status failure: HTTP ' . $httpCode . ' - ' . $error);

            // For testing/fallback simulation if external server is mock/not fully connected
            // kita bisa kembalikan data mock untuk validasi di local environment.
            // Di lingkungan production sesungguhnya, ini harus mengembalikan null.
            if (env('CI_ENVIRONMENT') === 'development') {
                return [
                    'IdBilling' => $idBilling,
                    'NoDokumen' => 'SIMULASI-DOC-12345',
                    'Nominal' => 50000,
                    'Status' => 'LUNAS',
                    'TglBayar' => date('Y-m-d H:i:s')
                ];
            }
            return null;
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($result['Status'])) {
            log_message('error', 'Billing API Check Status invalid response: ' . $response);
            return null;
        }

        return $result;
    }
}
