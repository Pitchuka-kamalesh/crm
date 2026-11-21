<?php

namespace Modules\Finance\Services;

class PayUService
{
    protected string $key;
    protected string $salt;
    protected int $envProd;
    protected string $gatewayUrl;

    public function __construct()
    {
        $this->key = config('services.payu.key', 'TrOeqi');
        $this->salt = config('services.payu.salt', 'oUwlPbUgo653mTYzLfv2flsuCRdYqfkv');
        $this->envProd = (int) config('services.payu.env_prod', 0);
        $this->gatewayUrl = $this->envProd === 1 
            ? 'https://secure.payu.in/_payment' 
            : 'https://test.payu.in/_payment';
    }

    public function isProduction(): bool
    {
        return $this->envProd === 1;
    }

    public function getGatewayUrl(): string
    {
        return $this->gatewayUrl;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * Generate SHA-512 request hash for PayU.
     */
    public function generateHash(array $params): string
    {
        $txnid = $params['txnid'] ?? '';
        $amount = number_format((float) ($params['amount'] ?? 0), 2, '.', '');
        $productinfo = $params['productinfo'] ?? 'ADMISSION_FEE';
        $firstname = $params['firstname'] ?? '';
        $email = $params['email'] ?? '';
        $udf1 = $params['udf1'] ?? '';
        $udf2 = $params['udf2'] ?? '';
        $udf3 = $params['udf3'] ?? '';
        $udf4 = $params['udf4'] ?? '';
        $udf5 = $params['udf5'] ?? '';

        $hashString = $this->key . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $firstname . '|' . $email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $this->salt;

        return strtolower(hash('sha512', $hashString));
    }

    /**
     * Verify PayU response hash.
     */
    public function verifyHash(array $params): bool
    {
        if (empty($params['hash'])) {
            return false;
        }

        $key = $params['key'] ?? $this->key;
        $txnid = $params['txnid'] ?? '';
        $amount = $params['amount'] ?? '';
        $productinfo = $params['productinfo'] ?? '';
        $firstname = $params['firstname'] ?? '';
        $email = $params['email'] ?? '';
        $status = $params['status'] ?? '';
        $resphash = strtolower($params['hash']);

        $udf1 = $params['udf1'] ?? '';
        $udf2 = $params['udf2'] ?? '';
        $udf3 = $params['udf3'] ?? '';
        $udf4 = $params['udf4'] ?? '';
        $udf5 = $params['udf5'] ?? '';
        $udf6 = $params['udf6'] ?? '';
        $udf7 = $params['udf7'] ?? '';
        $udf8 = $params['udf8'] ?? '';
        $udf9 = $params['udf9'] ?? '';
        $udf10 = $params['udf10'] ?? '';

        // Official PayU reverse hash format:
        // sha512(additionalCharges|salt|status|udf10|udf9|udf8|udf7|udf6|udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)
        $reverseKeyString = $udf10 . '|' . $udf9 . '|' . $udf8 . '|' . $udf7 . '|' . $udf6 . '|' . $udf5 . '|' . $udf4 . '|' . $udf3 . '|' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;

        if (!empty($params['additionalCharges'])) {
            $calcHash = strtolower(hash('sha512', $params['additionalCharges'] . '|' . $this->salt . '|' . $status . '|' . $reverseKeyString));
        } else {
            $calcHash = strtolower(hash('sha512', $this->salt . '|' . $status . '|' . $reverseKeyString));
        }

        return hash_equals($resphash, $calcHash);
    }
}