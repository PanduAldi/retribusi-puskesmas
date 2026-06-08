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

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Security Exception for Internal Network as per context
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
            log_message('error', 'Billing API failure: HTTP ' . $httpCode . ' - ' . $error);
            return null;
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($result['IdBilling'])) {
            log_message('error', 'Billing API invalid response: ' . $response);
            return null;
        }

        return $result;
    }
}
