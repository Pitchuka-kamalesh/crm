<?php

namespace App\Services;

use App\Models\CommunicationLog;
use App\Models\Wapplog;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send OTP via SmartPing WhatsApp API.
     *
     * @param string $phone Destination mobile number
     * @param string $otp 6-digit OTP code
     * @param string|null $user User identifier / employee code / student name
     * @param string|null $registerNo Register number or application number
     * @param string|null $ip Remote IP address
     * @return array
     */
    public function sendOtp(
        string $phone,
        string $otp,
        ?string $user = null,
        ?string $registerNo = null,
        ?string $ip = null
    ): array {
        $cfg = config('services.smartping');
        $app_name = config('app.name');
        $cleanPhone = $this->cleanDestination($phone);

        if (strlen($cleanPhone) !== 10) {
            Log::warning("[WhatsAppService] Invalid phone number for WhatsApp OTP: {$phone}");
            return [
                'success' => false,
                'status' => 'INVALID_PHONE',
                'message' => 'Destination mobile must be 10 digits.',
            ];
        }

        $apiUrl = $cfg['url'] ?? 'https://backend.api-wa.co/campaign/smartping/api/v2';
        $apiKey = $cfg['api_key'];
        $campaign = $cfg['otp_campaign'] ?? 'crmadm';
        $userName = $cfg['user_name'] ?? 'Vignan University Tech';
        $source = $cfg['source'] ?? 'new-landing-page form';
        $mode = strtoupper($cfg['otp_mode'] ?? 'ONE_VAR');

//        dd($cfg);
        $var1OtpValue = "Admission Portal login OTP is *{$otp}*";
        $var2MatterText = match ($user) {
            'admission' => 'Welcome to'.$app_name.' ! Please complete your basic details and proceed with the admission process.\n',

            'crm' => 'Welcome to'.$app_name.'Please use this OTP to verify your employee account and proceed with the requested service.',
            default => 'Please use this OTP to verify your account and complete the requested process.',
        };
        $messageText = "{$app_name} Alert: {$var2MatterText}  \nOTP: {$var1OtpValue}";

        $templateParams = match ($mode) {
            'ONE_VAR' => [$var1OtpValue],
            'TWO_VARS' => [$messageText],
            default => [$messageText]
        };
        $buttons = [];
        if ($mode === 'AUTH_OTP') {
            $buttons = [
                [
                    'type' => 'button',
                    'sub_type' => 'url',
                    'index' => 0,
                    'parameters' => [
                        ['type' => 'text', 'text' => $otp],
                    ],
                ],
            ];
        }

        $formattedDestination = str_starts_with($cleanPhone, '91') && strlen($cleanPhone) === 12
            ? ('+' . $cleanPhone)
            : ('+91' . $cleanPhone);

        $payload = [
            'apiKey' => $apiKey,
            'campaignName' => $campaign,
            'destination' => $formattedDestination,
            'userName' => $userName,
            'templateParams' => $templateParams,
            'source' => $source,
            'media' => (object)[],
            'buttons' => $buttons,
            'carouselCards' => [],
            'location' => (object)[],
            'attributes' => (object)[],
            'paramsFallbackValue' => (object)[],
        ];

        return $this->dispatchCampaign(
            payload: $payload,
            apiUrl: $apiUrl,
            phone: $cleanPhone,
            messageText: $messageText,
            campaign: $campaign,
            var1: $otp,
            var2: $var2MatterText,
            user: $user,
            registerNo: $registerNo,
            ip: $ip ?? request()?->ip() ?? '127.0.0.1'
        );
    }

    /**
     * Send Admission Confirmation / Information via SmartPing WhatsApp API.
     *
     * @param string $phone
     * @param string $candidateName
     * @param string $applicationId
     * @param string $programName
     * @param string|null $contactInfo
     * @param string|null $user
     * @param string|null $registerNo
     * @param string|null $ip
     * @return array
     */
    public function sendAdmissionInfo(
        string $phone,
        string $candidateName,
        string $applicationId,
        string $programName,
        ?string $contactInfo = null,
        ?string $user = null,
        ?string $registerNo = null,
        ?string $ip = null
    ): array {
        $cfg = config('services.smartping');
        $cleanPhone = $this->cleanDestination($phone);

        $apiUrl = $cfg['url'] ?? 'https://backend.api-wa.co/campaign/smartping/api/v2';
        $apiKey = $cfg['api_key'];
        $campaign = $cfg['admission_campaign'] ?? 'crmfirstinfo';
        $userName = $cfg['user_name'] ?? 'Vignan University Tech';
        $source = $cfg['source'] ?? 'new-landing-page form';
        $contact = $contactInfo ?? 'admissions@vignan.ac.in / 0863-2344700';

        $templateParams = [
            $candidateName,
            $applicationId,
            $programName,
            $contact,
        ];

        $messageText = "Dear {$candidateName}, In Admission process, You applied program: {$programName}. Application No: {$applicationId}. Contact: {$contact} -- Vignan University.";

        $formattedDestination = str_starts_with($cleanPhone, '91') && strlen($cleanPhone) === 12
            ? ('+' . $cleanPhone)
            : ('+91' . $cleanPhone);

        $payload = [
            'apiKey' => $apiKey,
            'campaignName' => $campaign,
            'destination' => $formattedDestination,
            'userName' => $userName,
            'templateParams' => $templateParams,
            'source' => $source,
            'media' => (object)[],
            'buttons' => [],
            'carouselCards' => [],
            'location' => (object)[],
            'attributes' => (object)[],
            'paramsFallbackValue' => (object)[],
        ];

        return $this->dispatchCampaign(
            payload: $payload,
            apiUrl: $apiUrl,
            phone: $cleanPhone,
            messageText: $messageText,
            campaign: $campaign,
            var1: $candidateName,
            var2: $applicationId,
            user: $user ?? $candidateName,
            registerNo: $registerNo ?? $applicationId,
            ip: $ip ?? request()?->ip() ?? '127.0.0.1'
        );
    }

    /**
     * Dispatch HTTP POST to SmartPing API and record tracker log.
     */
    protected function dispatchCampaign(
        array $payload,
        string $apiUrl,
        string $phone,
        string $messageText,
        string $campaign,
        ?string $var1 = null,
        ?string $var2 = null,
        ?string $user = null,
        ?string $registerNo = null,
        ?string $ip = null
    ): array {
        $status = 'FAILED';
        $traceId = null;
        $responseBody = [];

        try {
            $response = Http::timeout(6)
                ->connectTimeout(3)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($apiUrl, $payload);

            $responseBody = $response->json() ?? ['raw' => $response->body()];
            $traceId = $this->extractTraceId($responseBody);

            if ($response->successful()) {
                $status = 'SUBMITTED';
                Log::info("[WhatsAppService] Dispatched campaign '{$campaign}' to {$phone}", [
                    'trace_id' => $traceId,
                    'status' => $response->status(),
                ]);
            } else {
                $status = 'HTTP_' . $response->status();
                Log::warning("[WhatsAppService] SmartPing returned error {$response->status()}", [
                    'destination' => $phone,
                    'response' => $responseBody,
                ]);
            }
        } catch (Exception $e) {
            $status = 'EXCEPTION';
            $responseBody = ['error' => $e->getMessage()];
            Log::error("[WhatsAppService] Exception sending to {$phone}: " . $e->getMessage());
        }

        // Record in master.wapp_logs (guarantees single record per destination phone for whatsapp channel)
        try {
            $log = CommunicationLog::updateOrCreate(
                [
                    'wapptophone' => $phone,
                    'channel'     => 'whatsapp',
                ],
                [
                    'wauser'           => $user ?? 'SYSTEM_USER',
                    'waregisterno'     => $registerNo ?? '',
                    'jiotraceid'       => $traceId,
                    'template_name'    => $campaign,
                    'wapptext'         => $messageText,
                    'var1'             => $var1,
                    'var2'             => $var2,
                    'status'           => $status,
                    'ipaddr'           => $ip ?? '127.0.0.1',
                    'response_payload' => $responseBody,
                ]
            );

            return [
                'success' => ($status === 'SUBMITTED'),
                'status' => $status,
                'trace_id' => $traceId,
                'log_id' => $log->id,
                'data' => $responseBody,
            ];
        } catch (Exception $e) {
            Log::warning('[WhatsAppService] Could not write to master.wapp_logs: ' . $e->getMessage());

            return [
                'success' => ($status === 'SUBMITTED'),
                'status' => $status,
                'trace_id' => $traceId,
                'log_id' => null,
                'data' => $responseBody,
            ];
        }
    }

    /**
     * Clean phone number to exactly 10 digits without country prefix.
     */
    public function cleanDestination(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '91') && strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }
        if (strlen($digits) > 10) {
            $digits = substr($digits, -10);
        }
        return $digits;
    }

    /**
     * Extract trace ID / message ID from SmartPing response.
     */
    protected function extractTraceId(array $body): ?string
    {
        $keys = ['submitted_message_id', 'messageId', 'message_id', 'id', 'trace_id', 'msgId'];
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

        return null;
    }
}

