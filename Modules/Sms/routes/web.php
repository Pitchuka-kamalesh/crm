<?php

use Illuminate\Support\Facades\Route;
use Modules\Sms\Http\Controllers\SmsController;

Route::middleware(['auth'])->group(function () {
    Route::get('sms', [SmsController::class, 'index'])->name('sms.index');
    Route::get('sms/index', [SmsController::class, 'index']);
    Route::post('sms/test-whatsapp', [SmsController::class, 'sendTestWhatsApp'])->name('sms.test-whatsapp');
    Route::post('sms/test-sms', [SmsController::class, 'sendTestSms'])->name('sms.test-sms');
    Route::get('sms/{id}', [SmsController::class, 'show'])->name('sms.show');
});
