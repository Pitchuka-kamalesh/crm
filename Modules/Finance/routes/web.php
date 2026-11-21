<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\FinanceController;
use Modules\Finance\Http\Controllers\CoupounController;
use Modules\Finance\Http\Controllers\PayUController;

// ── 1. PUBLIC: PayU Callback (surl & furl) ──
Route::post('/payu/response', [PayUController::class, 'handleResponse'])
    ->withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, AddQueuedCookiesToResponse::class, ValidateCsrfToken::class])
    ->name('payu.response');



// ── 3. AUTHENTICATED: Dashboard routes ──
Route::middleware(['auth','role:3'])->group(function () {
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/index', [FinanceController::class, 'index']);
    Route::get('/finance/paiddetails', [FinanceController::class, 'index'])->name('finance.paiddetails');

    Route::post('/crm/coupons/generate-personal', [CoupounController::class, 'generatePersonalCoupon'])->name('crm.coupons.generate-personal');
    Route::post('/crm/percoupongen', [CoupounController::class, 'generatePersonalCoupon']);
});
Route::middleware(['auth','role:5'])->group(function () {
    Route::post('/payu/initiate', [PayUController::class, 'initiate'])->name('payu.initiate');
    // Route::post('/payu/cancel',   [PayUController::class, 'cancel'])->name('payu.cancel');
    // ── 2. PUBLIC: Signed URL Receipt (for post-payment redirect) ──
Route::get('/payu/receipt', [PayUController::class, 'receipt'])
    ->name('payu.receipt');
    // Authenticated receipt view
    Route::get('/payu/payu_receipt', [PayUController::class, 'payu_receipt'])->name('payu.payu_receipt');
});