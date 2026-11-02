<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TeamsWebhookService
{
    /**
     * Send a Teams message.
     */
    public function send(string $message): bool
    {
        if (!config('teams.web_alert.enabled')) {
            return false;
        }

        $url = config('teams.web_alert.url');

        if (empty($url)) {
            Log::warning('Teams webhook URL not configured.');
            return false;
        }

        try {
            $response = Http::timeout(
                config('teams.timeout', 15)
            )->post($url, [
                'text' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Teams webhook delivery failed.', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return false;
            }

            Log::info('Teams webhook delivered.', [
                'status' => $response->status(),
            ]);

            return true;

        } catch (Throwable $e) {

            Log::error('Teams webhook exception.', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send an Adaptive Card.
     */
    public function sendCard(
        string $title,
        string $message
    ): bool {

        if (!config('teams.web_alert.enabled')) {
            return false;
        }

        $url = config('teams.web_alert.url');

        if (empty($url)) {
            Log::warning('Teams webhook URL not configured.');
            return false;
        }

        try {
            $response = Http::timeout(
                config('teams.timeout', 15)
            )->post($url, [
                'type' => 'message',
                'attachments' => [
                    [
                        'contentType' =>
                            'application/vnd.microsoft.card.adaptive',
                        'contentUrl' => null,
                        'content' => [
                            '$schema' =>
                                'http://adaptivecards.io/schemas/adaptive-card.json',
                            'type' => 'AdaptiveCard',
                            'version' => '1.2',
                            'body' => [
                                [
                                    'type' => 'TextBlock',
                                    'text' => $title,
                                    'weight' => 'Bolder',
                                    'size' => 'Large',
                                ],
                                [
                                    'type' => 'TextBlock',
                                    'text' => $message,
                                    'wrap' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Teams Adaptive Card failed.', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return false;
            }

            Log::info('Teams Adaptive Card delivered.', [
                'status' => $response->status(),
            ]);

            return true;

        } catch (Throwable $e) {

            Log::error('Teams Adaptive Card exception.', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
