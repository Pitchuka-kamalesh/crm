<?php

namespace App\Services;

use App\Models\CommunicationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS via JioCX DLT Gateway.
     *
     * @param string $to 10-digit mobile number
     * @param string $body Formatted message matching approved DLT template
     * @param string $templateId DLT Template ID
     * @param string|null $user User / Emp / Student identifier
     * @param string|null $registerNo Register or application number
     * @return array
     */
    public function send(
        string $to,
        string $body,
        string $templateId,
        ?string $user = null,
        ?string $registerNo = null
    ): array {
        $cfg = config('services.jiocx_sms');

        // Clean recipient number (remove non-digits, leading +91 or 0)
        $cleanTo = preg_replace('/[^0-9]/', '', $to);
        if (strlen($cleanTo) === 11 && str_starts_with($cleanTo, '0')) {
            $cleanTo = substr($cleanTo, 1);
        } elseif (strlen($cleanTo) === 12 && str_starts_with($cleanTo, '91')) {
            $cleanTo = substr($cleanTo, 2);
        } elseif (strlen($cleanTo) > 10) {
            $cleanTo = substr($cleanTo, -10);
        }

        if (empty($cleanTo)) {
            Log::warning('[SmsService] Skipping SMS send: recipient phone number is empty.');
            return ['success' => false, 'status' => 0, 'data' => ['error' => 'Recipient phone number is empty']];
        }

        if (empty($cfg['password']) || empty($cfg['url'])) {
            Log::info("[SmsService] JioCX SMS gateway password is empty in configuration. Skipping remote HTTP request.");
            return [
                'success' => false,
                'status' => 'CREDENTIALS_PENDING',
                'data' => ['message' => 'JioCX password pending in .env'],
            ];
        }

        $status = 'PENDING';
        $traceId = null;
        $data = [];

        try {
            $response = Http::timeout(5)
                ->connectTimeout(3)
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
            $data = $response->json() ?? ['raw' => $response->body()];
            $status = $success ? 'SUBMITTED' : ('HTTP_' . $response->status());
            $traceId = $this->extractTraceId($data);

            if ($success) {
                Log::info("[SmsService] SMS dispatched successfully to '{$cleanTo}'.", [
                    'template_id' => $templateId,
                    'response'    => $data,
                ]);
            } else {
                Log::warning("[SmsService] SMS delivery returned non-200 status for '{$cleanTo}'.", [
                    'status'   => $response->status(),
                    'response' => $data,
                ]);
            }
        } catch (\Throwable $e) {
            $success = false;
            $status = 'EXCEPTION';
            $data = ['error' => $e->getMessage()];
            Log::error('[SmsService] SMS send failed', ['to' => $cleanTo, 'error' => $e->getMessage()]);
        }

        // Record in communication logs table
        try {
            CommunicationLog::create([
                'channel' => 'sms',
                'wauser' => $user ?? 'SYSTEM_USER',
                'waregisterno' => $registerNo ?? '',
                'wapptophone' => $cleanTo,
                'jiotraceid' => $traceId,
                'template_name' => $templateId,
                'wapptext' => $body,
                'var1' => $cleanTo,
                'status' => $status,
                'ipaddr' => request()?->ip() ?? '127.0.0.1',
                'response_payload' => is_array($data) ? $data : ['response' => $data],
            ]);
        } catch (\Throwable $e) {
            Log::warning('[SmsService] Could not write SMS communication log: ' . $e->getMessage());
        }

        return [
            'success' => $success,
            'status'  => $status,
            'trace_id' => $traceId,
            'data'    => $data,
        ];
    }

    /**
     * Send Admission details SMS via Jio DLT template 1777179354065677511.
     */
    public function sendAdmissionSms(
        string $phone,
        string $name,
        string $program,
        string $branch,
        string $campus,
        string $applicationNo
    ): array {
        $templateId = config('services.jiocx_sms.template_admission', '1777179354065677511');

        $msg = "Dear {$name},\n"
             . "In Admission process,\n"
             . "You applied program details\n"
             . "Program: {$program}\n"
             . "Branch: {$branch}\n"
             . "Campus: {$campus}\n"
             . "your application number: {$applicationNo}, this your for future process identical number.\n"
             . "-- Vignan University.";

        return $this->send(
            to: $phone,
            body: $msg,
            templateId: $templateId,
            user: $name,
            registerNo: $applicationNo
        );
    }

    /**
     * Send account credentials using the approved Vignan DLT template.
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

        return $this->send($phone, $msg, $templateId, $username, $username);
    }

    /**
     * Send OTP for login verification.
     */
    public function sendOtp(string $phone, string $otp, ?string $user = null, ?string $registerNo = null): array
    {
        $templateId = config('services.jiocx_sms.template_otp') ?: env('JIOCX_TEMPLATE_ID_OTP');
        
        $msg = "Your Vignan LMS verification code is: {$otp}. Valid for 10 minutes. Do not share this OTP with anyone.\n\n-- Vignan University.";

        if (!$templateId) {
            Log::info("[SmsService] DLT template for OTP not configured; logging OTP for '{$phone}': {$otp}");
            return ['success' => true, 'status' => 200, 'data' => ['simulated' => true, 'otp' => $otp]];
        }

        return $this->send($phone, $msg, $templateId, $user, $registerNo);
    }

    /**
     * Extract trace ID from JioCX SMS response.
     */
    protected function extractTraceId(array|string $body): ?string
    {
        if (is_array($body)) {
            $keys = ['trace_id', 'message_id', 'id', 'requestId', 'msg_id'];
            foreach ($keys as $k) {
                if (!empty($body[$k])) {
                    return (string) $body[$k];
                }
            }
        }
        return null;
    }
}
