<?php

namespace Modules\Mail\Services;

use Modules\Mail\Mail\GenericNotificationMail; // Ensure this matches the namespace in File 1
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    protected function sendEmail(string $toEmail, string $subject, string $title, string $studentName, array $data): array
    {
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            Log::warning("[MailService] Skipping email send: invalid or empty email address '{$toEmail}'.");
            return ['success' => false, 'message' => 'Invalid email address'];
        }

        try {
            Mail::to($toEmail)->send(new GenericNotificationMail($subject, $title, $studentName, $data));

            Log::info("[MailService] Email '{$subject}' sent successfully to '{$toEmail}'.");
            return ['success' => true, 'message' => 'Email sent successfully'];

        } catch (\Throwable $e) {
            Log::error("[MailService] Failed to send email '{$subject}' to '{$toEmail}'.", [
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()];
        }
    }

    public function sendAdmissionDetails(string $toEmail, array $details): array
    {
        return $this->sendEmail(
            toEmail: $toEmail,
            subject: 'Vignan University - Admission Application Details',
            title: 'Your admission application has been successfully received.',
            studentName: $details['name'] ?? 'Student',
            data: [
                'Application No' => $details['application_no'] ?? 'N/A',
                'Program'        => $details['program'] ?? 'N/A',
                'Branch'         => $details['branch'] ?? 'N/A',
                'Campus'         => $details['campus'] ?? 'N/A',
                'Phone Number'   => $details['phone'] ?? 'N/A',
            ]
        );
    }
}
