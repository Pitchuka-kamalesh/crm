<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\TeamsWebhookServiceServiceProvider::class,
    App\Providers\SmsServiceProvider::class,
    App\Providers\TeamsWebhookServiceProvider::class,
];
