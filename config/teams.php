<?php

return [
    'web_alert' => [
        'enabled' => env('TEAMS_WEBHOOK_WEB_ALERT_ENABLE', false),
        'url' => env('TEAMS_WEBHOOK_WEB_ALERT'),
    ],

    'timeout' => 15,
];

