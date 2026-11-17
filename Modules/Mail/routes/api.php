<?php

use Illuminate\Support\Facades\Route;
use Modules\Mail\Http\Controllers\MailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('mails', MailController::class)->names('mail');
});
