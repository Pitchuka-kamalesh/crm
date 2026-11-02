<?php

namespace App\Services;

use App\Mail\UserWelcomeMail;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class UserWelcomeNotificationService
{
    /**
     * Send welcome email and SMS credentials with password change instruction.
     *
     * @param User $user
     * @param string $plainPassword
     * @param Profile|null $profile
     * @return bool
     */
    public static function sendWelcomeCredentials(User $user, string $plainPassword, ?Profile $profile = null): bool
    {
        $profile = $profile ?? $user->profile;
        $email = $profile?->email;
        $phone = $profile?->phone;
        $dispatched = false;

        // 1. Dispatch SMS Credentials via JioCX Gateway
        if (!empty($phone)) {
            try {
                $sms = app(SmsService::class);
                $smsResult = $sms->sendCredentials($phone, $user->username, $plainPassword);
                if (!empty($smsResult['success'])) {
                    Log::info("[UserWelcomeNotification] SMS credentials sent successfully to '{$phone}' for user '{$user->username}'.");
                    $dispatched = true;
                } else {
                    Log::warning("[UserWelcomeNotification] SMS credentials dispatch failed for '{$phone}' ({$user->username}).", ['result' => $smsResult]);
                }
            } catch (Throwable $e) {
                Log::warning("[UserWelcomeNotification] Exception sending SMS to '{$phone}' for user '{$user->username}': " . $e->getMessage());
            }
        } else {
            Log::info("[UserWelcomeNotification] No phone number available for SMS delivery for user '{$user->username}'.");
        }

        // 2. Dispatch Welcome Email
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($email)->send(new UserWelcomeMail($user, $plainPassword, $profile));
                Log::info("[UserWelcomeMail] Welcome email sent successfully to '{$email}' for user '{$user->username}'.");
                $dispatched = true;
            } catch (Throwable $e) {
                Log::warning("[UserWelcomeMail] Failed sending email to '{$email}' for user '{$user->username}': " . $e->getMessage());
            }
        } else {
            Log::info("[UserWelcomeMail] Skipped email for user '{$user->username}': No valid email address provided.");
        }

        return $dispatched;
    }
}
