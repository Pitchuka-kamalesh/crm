<?php

use Illuminate\Support\Facades\Route;
use Modules\Mail\Http\Controllers\MailController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('mails', MailController::class)->names('mail');
});
