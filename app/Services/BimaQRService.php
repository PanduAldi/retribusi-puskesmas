<?php

namespace App\Services;

class BimaQRService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected string $key;

    // Token cache (di simpan dulu, expire setelah 5 menit)
    protected ?string $cachedToken = null;
    protected ?string $cachedAt = null;

    public function __construct()
    {
        $this->baseUrl  = rtrim((string) env('BIMA_QR_URL', ''), '/');
        $this->username = (string) env('BIMA_QR_USERNAME', '');
        $this->password = (string) env('BIMA_QR_PASSWORD', '');
        $this->key      = (string) env('BIMA_QR_KEY', '');
    }

    /**
     * Generate QRIS payment link using No. RM
     * Format idBilling = "77" + no_rm
     * Token expires within 5 minutes (per dokumen BIMAQR)
     */
    public function getQrisLinkByNoRm(string $noRm): ?string
    {
        $idBilling = "77" . $noRm;
        $token = $this->getNewToken($idBilling);
        if (empty($token)) {
            log_message('error', 'BIMAQR: Failed to get token for no_rm={no_rm}', ['no_rm' => $noRm]);
            return null;
        }
        return $this->getPaymentLink($token);
    }

    /**
     * Backward compatibility: get payment link by id_billing (legacy)
     */
    public function getPaymentLinkByIdBilling(string $idBilling): ?string
    {
        $token = $this->getNewToken($idBilling);
        if (empty($token)) {
            return null;
        }
        return $this->getPaymentLink($token);
    }

    /**
     * Get NEW token from BIMAQR
     * Token valid 5 minutes, so always request fresh token
     */
    protected function getNewToken(string $idBilling): ?string
    {
        $response = $this->postJson('/getToken', [
            'idBilling' => $idBilling,
            'key'       => $this->key,
        ]);

        if (!$this->isSuccessResponse($response) || empty($response['token'])) {
            log_message('error', 'BIMAQR getToken failed for billing {id}. Error code: {code}', [
                'id'   => $idBilling,
                'code' => $response['errCode'] ?? 'NO_RESPONSE',
            ]);
            return null;
        }

        return (string) $response['token'];
    }

    /**
     * Legacy method – deprecated, use getNewToken()
     */
    public function getToken(string $idBilling): ?string
    {
        return $this->getNewToken($idBilling);
    }

    public function getPaymentLink(string $token): ?string
    {
        $response = $this->postJson('/getLink', [
            'token' => $token,
        ]);

        if (!$this->isSuccessResponse($response) || empty($response['data'])) {
            log_message('error', 'BIMAQR getLink failed. Error code: {code}', [
                'code' => $response['errCode'] ?? 'NO_RESPONSE',
            ]);
            return null;
        }

        return (string) $response['data'];
    }

    protected function postJson(string $path, array $payload): ?array
    {
        if ($this->baseUrl === '' || $this->username === '' || $this->password === '' || $this->key === '') {
            log_message('error', 'BIMAQR configuration is incomplete.');
            return null;
        }

        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $responseBody = curl_exec($ch);
        $curlError    = curl_error($ch);
        $httpCode     = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseBody === false || $curlError !== '' || $httpCode < 200 || $httpCode >= 300) {
            log_message('error', 'BIMAQR API error on {path}: HTTP {code}, cURL: {error}', [
                'path'  => $path,
                'code'  => $httpCode,
                'error' => $curlError,
            ]);
            return null;
        }

        $decoded = json_decode((string) $responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            log_message('error', 'BIMAQR API invalid JSON response on {path}.', ['path' => $path]);
            return null;
        }

        return $decoded;
    }

    protected function isSuccessResponse(?array $response): bool
    {
        return is_array($response) && (($response['errCode'] ?? null) === '00');
    }
}