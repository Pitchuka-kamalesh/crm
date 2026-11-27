<?php

namespace Modules\Sms\Services;

use Modules\Sms\Models\CommunicationLog;
use Modules\Sms\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS via JioCX DLT Gateway.
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

        $destTo = (strlen($cleanTo) === 10) ? ($cleanTo) : $cleanTo;

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->acceptJson()
                ->post($cfg['url'], [
                    'username'         => $cfg['username'],
                    'password'         => $cfg['password'],
                    'sender_id'        => $cfg['sender_id'],
                    'to'               => $destTo,
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
                Log::info("[SmsService] SMS dispatched successfully to '{$destTo}'.", [
                    'template_id' => $templateId,
                    'body_preview' => substr($body, 0, 100),
                    'response'    => $data,
                ]);
            } else {
                Log::warning("[SmsService] SMS delivery returned non-200 status for '{$destTo}'.", [
                    'status'   => $response->status(),
                    'response' => $data,
                ]);
            }
        } catch (\Throwable $e) {
            $success = false;
            $status = 'EXCEPTION';
            $data = ['error' => $e->getMessage()];
            Log::error('[SmsService] SMS send failed', ['to' => $destTo, 'error' => $e->getMessage()]);
        }

        $now = now();
        $ip = request()?->ip() ?? '127.0.0.1';

        // 1. Store directly into master.smslogs (primary official SMS logs table)
        try {
            SmsLog::create([
                'smsuser'       => (string)($user ?? 'SYSTEM_USER'),
                'smsregisterno' => (string)($registerNo ?? ''),
                'smstophone'    => (string)$cleanTo,
                'jiotraceid'    => (string)($traceId ?? ''),
                'smstext'       => (string)$body,
                'datetime'      => $now,
                'ipaddr'        => (string)$ip,
                'status'        => (string)$status,
                'var1'          => (string)$cleanTo,
                'var2'          => (string)$templateId,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[SmsService] Could not write to master.smslogs: ' . $e->getMessage());
        }

        // 2. Also record in master.wapp_logs / communication_logs for unified dashboard tracking
        try {
            CommunicationLog::create([
                'channel'          => 'sms',
                'wapptophone'      => $cleanTo,
                'wauser'           => $user ?? 'SYSTEM_USER',
                'waregisterno'     => $registerNo ?? '',
                'jiotraceid'       => $traceId,
                'template_name'    => $templateId,
                'wapptext'         => $body,
                'var1'             => $cleanTo,
                'status'           => $status,
                'ipaddr'           => $ip,
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
     * Send Admission details SMS using the exact registered DLT template:
     *
     * Dear {#var#},
     *
     * In Admission process,
     * You applied program details
     * Program: {#var#}
     * Branch: {#var#}
     * Campus: {#var#}
     * your application number: {#var#}, this your for future process identical number.
     * -- Vignan University.
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

        // Must match the registered DLT template exactly (only {#var#} slots replaced)
        $msg = "Dear {$name},\n"
             . "\n"
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

        // Approved DLT credential template format
        $msg = "Dear Student,\n"
             . "Welcome to Vignan University!\n"
             . "Your LMS account has been created.\n"
             . "Username: {$username}\n"
             . "Password: {$password}\n"
             . "Please login and change your password immediately.\n"
             . "-- Vignan University.";

        return $this->send($phone, $msg, $templateId, $username, $username);
    }

    /**
     * Send OTP for login verification using the approved DLT template:
     *
     * Dear Student,
     * Welcome to Vignan University!
     * Your OTP for admission onboarding is {#var#}
     * Use it to continue your registration.
     * -- Vignan University.
     */
    public function sendOtp(string $phone, string $otp, ?string $user = null, ?string $registerNo = null)
    {
        $templateId = config('services.jiocx_sms.template_otp') ?: env('JIOCX_TEMPLATE_ID_OTP');
        error_log("template id : $templateId");

        $msg = "Dear Student,\n"
             . "Welcome to Vignan University!\n"
             . "Your OTP for admission onboarding is {$otp}\n"
             . "Use it to continue your registration.\n"
             . "-- Vignan University.";

        if (!$templateId) {
            Log::info("[SmsService] DLT template for OTP not configured; logging OTP for '{$phone}': {$otp}");
            return ['success' => true, 'status' => 200, 'data' => ['simulated' => true, 'otp' => $otp]];
        }

        return $this->send($phone, $msg, $templateId, $user, $registerNo);
    }

    /**
     * Send stage / milestone SMS notification via JioCX SMS gateway.
     */
    public function sendStageSms(
        string $phone,
        string $message,
        ?string $name = null,
        ?string $applicationNo = null
    ): array {
        $templateId = config('services.jiocx_sms.template_stage') 
            ?: env('JIOCX_DLT_TEMPLATE_ID_STAGE') 
            ?: config('services.jiocx_sms.template_otp', '1777179353790244153');

        return $this->send(
            to: $phone,
            body: $message,
            templateId: $templateId,
            user: $name ?? 'Student',
            registerNo: $applicationNo
        );
    }

    /**
     * Extract trace ID from JioCX SMS response.
     */
    protected function extractTraceId(array|string $body): ?string
    {
        if (is_array($body)) {
            $keys = ['transaction_id', 'trace_id', 'message_id', 'id', 'requestId', 'msg_id', 'txnid'];
            foreach ($keys as $k) {
                if (!empty($body[$k])) {
                    return (string) $body[$k];
                }
            }

            if (isset($body['data']) && is_array($body['data'])) {
                foreach ($keys as $k) {
                    if (!empty($body['data'][$k])) {
                        return (string) $body['data'][$k];
                    }
                }
            }
        }
        return null;
    }
}
