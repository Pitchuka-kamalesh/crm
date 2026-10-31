<?php

namespace App\Services;

use App\Mail\ServerErrorReported;
use App\Models\Profile;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ErrorMailAlertService
{
    /**
     * Send an unhandled exception or critical error alert via Email to administrators.
     *
     * @param Throwable $e
     * @param Request|null $request
     * @return bool
     */
    public static function sendException(Throwable $e, ?Request $request = null): bool
    {
        try {
            // Check if exception type is in the ignored list
            $ignored = [
                \Illuminate\Validation\ValidationException::class,
                \Illuminate\Auth\AuthenticationException::class,
                \Illuminate\Auth\Access\AuthorizationException::class,
                \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
                \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
                \Illuminate\Session\TokenMismatchException::class,
                \Illuminate\Http\Exceptions\PostTooLargeException::class,
            ];

            foreach ($ignored as $ignoredClass) {
                if ($e instanceof $ignoredClass) {
                    return false;
                }
            }

            $request = $request ?: (app()->bound('request') ? request() : null);

            // Rate-limiting / deduplication (30 seconds) to prevent flooding the admin mailbox
            $rateLimit = 30;
            $hash = md5($e->getFile() . ':' . $e->getLine() . ':' . $e->getMessage() . ':' . ($request ? $request->path() : 'cli'));
            $cacheKey = 'mail_err_alert_' . $hash;

            if (Cache::has($cacheKey)) {
                return false;
            }
            Cache::put($cacheKey, true, now()->addSeconds($rateLimit));

            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            $url = $request ? $request->fullUrl() : (app()->runningInConsole() ? 'CLI / Artisan Console' : url('/'));
            $user = Auth::user();
            $userName = $user ? $user->full_name : 'Guest / System';
            $userEmail = $user?->profile?->email ?? ($user?->username ?? 'N/A');
            $userRole = $user ? $user->role : 'guest';
            $ip = $request ? $request->ip() : '127.0.0.1';
            $userAgent = $request ? $request->userAgent() : 'System Console';

            $errorDetails = [
                'status_code'       => $statusCode,
                'error_message'     => $e->getMessage() ?: get_class($e),
                'exception'         => get_class($e),
                'file'              => $e->getFile(),
                'line'              => $e->getLine(),
                'url'               => $url,
                'method'            => $request ? $request->method() : 'CLI',
                'user_name'         => $userName,
                'user_email'        => $userEmail,
                'user_role'         => $userRole,
                'ip'                => $ip,
                'user_agent'        => $userAgent,
                'reported_at'       => now()->format('M d, Y h:i A'),
                'activity_logs_url' => url('/activity-logs'),
            ];

            // 1. Log to ActivityLogger (Superadmin panel: /activity-logs)
            $activityLog = ActivityLogger::log(
                action: 'server_500_exception_captured',
                actionTitle: '500 Server Exception Alert',
                module: 'System',
                description: "Unhandled {$statusCode} exception: " . substr($e->getMessage() ?: get_class($e), 0, 250) . " on {$url} ({$e->getFile()}:{$e->getLine()})",
                severity: 'danger',
                options: [
                    'user'        => $user,
                    'entity_type' => 'ServerException',
                    'entity_name' => get_class($e),
                    'url'         => $url,
                    'payload'     => $errorDetails,
                ]
            );

            $errorDetails['activity_log_id'] = $activityLog?->id;

            // 2. Admin notification recipient email (web@vignan.ac.in)
            $recipients = config('mail.admin_alert_email', 'web@vignan.ac.in');

            // 3. Dispatch notification email to admin
            Mail::to($recipients)->send(new ServerErrorReported($errorDetails));

            return true;
        } catch (\Throwable $ex) {
            // Never break error handling
            Log::warning('ErrorMailAlertService: Delivery failed: ' . $ex->getMessage());
            return false;
        }
    }

    /**
     * Send a custom formatted error/alert email to admin.
     *
     * @param string $title
     * @param string $description
     * @param array $details
     * @return bool
     */
    public static function sendCustomAlert(string $title, string $description, array $details = []): bool
    {
        try {
            $recipients = config('mail.admin_alert_email', 'web@vignan.ac.in');

            $payload = array_merge([
                'status_code'       => 500,
                'error_message'     => $description,
                'url'               => url()->current(),
                'user_note'         => $title,
                'user_name'         => Auth::check() ? Auth::user()->full_name : 'System Alert',
                'user_email'        => 'System Monitor',
                'user_role'         => 'system',
                'ip'                => request()->ip() ?? '127.0.0.1',
                'user_agent'        => request()->userAgent() ?? 'System',
                'reported_at'       => now()->format('M d, Y h:i A'),
                'activity_logs_url' => url('/activity-logs'),
            ], $details);

            // Log to Activity Log
            $log = ActivityLogger::log(
                action: 'system_custom_alert_triggered',
                actionTitle: substr($title, 0, 50),
                module: 'System',
                description: $description,
                severity: 'danger',
                options: [
                    'payload' => $payload,
                    'url'     => $payload['url'] ?? url('/'),
                ]
            );

            $payload['activity_log_id'] = $log?->id;

            Mail::to($recipients)->send(new ServerErrorReported($payload));
            return true;
        } catch (\Throwable $e) {
            Log::warning('ErrorMailAlertService: sendCustomAlert failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a test error alert email to verify email connectivity.
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public static function sendTestPing(): array
    {
        try {
            $recipients = config('mail.admin_alert_email', 'web@vignan.ac.in');

            Mail::to($recipients)->send(new ServerErrorReported([
                'status_code'       => 500,
                'error_message'     => 'Test ping alert: Webhook/Mail alerting is operating properly.',
                'url'               => url('/'),
                'user_note'         => 'Admin manual test ping triggered.',
                'user_name'         => 'System Test Runner',
                'user_email'        => 'web@vignan.ac.in',
                'user_role'         => 'sa',
                'ip'                => '127.0.0.1',
                'user_agent'        => 'Artisan Console / Webhook Ping',
                'reported_at'       => now()->format('M d, Y h:i A'),
                'activity_logs_url' => url('/activity-logs'),
            ]));

            $recipientDisplay = is_array($recipients) ? implode(', ', $recipients) : $recipients;
            return [
                'success' => true,
                'message' => 'Test alert email dispatched successfully to ' . $recipientDisplay . '!',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to deliver test email alert: ' . $e->getMessage(),
            ];
        }
    }
}
