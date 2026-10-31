<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Send an SMS via JioCX DLT Gateway.
     *
     * @param string $to 10-digit mobile number
     * @param string $body Formatted message matching approved DLT template
     * @param string $templateId DLT Template ID
     * @return array
     */
    public function send(string $to, string $body, string $templateId): array
    {
        $cfg = config('services.jiocx_sms');

        // Clean recipient number (remove non-digits, leading +91 or 0)
        $cleanTo = preg_replace('/[^0-9]/', '', $to);
        if (strlen($cleanTo) === 11 && str_starts_with($cleanTo, '0')) {
            $cleanTo = substr($cleanTo, 1);
        } elseif (strlen($cleanTo) === 12 && str_starts_with($cleanTo, '91')) {
            $cleanTo = substr($cleanTo, 2);
        }

        if (empty($cleanTo)) {
            Log::warning('[SmsService] Skipping SMS send: recipient phone number is empty.');
            return ['success' => false, 'status' => 0, 'data' => ['error' => 'Recipient phone number is empty']];
        }

        try {
            $response = Http::timeout(10)
                ->connectTimeout(10)
                ->acceptJson()
                ->post($cfg['url'], [
                    'username'         => $cfg['username'],
                    'password'         => $cfg['password'],
                    'sender_id'        => $cfg['sender_id'],
                    'to'               => $cleanTo,
                    'sms_type'         => 'T',
                    'dlt_entity_id'    => $cfg['dlt_entity_id'],
                    'sms_content_type' => 'Static',
                    'body'             => $body,
                    'dlt_template_id'  => $templateId,
                ]);

            $success = $response->successful();
            $data = $response->json() ?? $response->body();

            if ($success) {
                Log::info("[SmsService] SMS dispatched successfully to '{$cleanTo}'.", [
                    'template_id' => $templateId,
                    'response'    => $data,
                    'message'     => $body,
                ]);
            } else {
                Log::warning("[SmsService] SMS delivery returned non-200 status for '{$cleanTo}'.", [
                    'status'   => $response->status(),
                    'response' => $data,
                    'message'  => $body,
                ]);
            }

            return [
                'success' => $success,
                'status'  => $response->status(),
                'data'    => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('[SmsService] SMS send failed', ['to' => $cleanTo, 'error' => $e->getMessage()]);
            return ['success' => false, 'status' => 0, 'data' => ['error' => $e->getMessage()]];
        }
    }

    /**
     * Send account credentials using the approved Vignan DLT template.
     *
     * @param string $phone
     * @param string $username
     * @param string $password
     * @return array
     */
    public function sendCredentials(string $phone, string $username, string $password): array
    {
        $templateId = config('services.jiocx_sms.template_create_account') ?: env('JIOCX_DLT_TEMPLATE_ID_Create_Account');
        if (!$templateId) {
            Log::error('[SmsService] Missing JIOCX_DLT_TEMPLATE_ID_Create_Account in configuration.');
            return ['success' => false, 'status' => 500, 'data' => ['error' => 'Template ID missing in configuration']];
        }

        $username = trim($username);
        $password = trim($password);

        $msg = "Dear Staff/Student,\n\n"
             . "An LMS mail id has been created. Please login and change your password immediately once you receive credentials. Here are the credentials for your account.\n"
             . "Username:{$username} and\n"
             . "Password: {$password}\n"
             . "Login through the link below.\n\n"
             . "-- Vignan University.";

        return $this->send($phone, $msg, $templateId);
    }
}
