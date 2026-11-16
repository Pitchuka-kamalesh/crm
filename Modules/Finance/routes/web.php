<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\FinanceController;
use Modules\Finance\Http\Controllers\CoupounController;
use Modules\Finance\Http\Controllers\PayUController;

Route::middleware(['auth'])->group(function () {
    // Finance Overview & Fee Verification
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/index', [FinanceController::class, 'index']);
    Route::get('/finance/paiddetails', [FinanceController::class, 'index'])->name('finance.paiddetails');

    // Coupon Actions
    Route::post('/crm/coupons/generate-personal', [CoupounController::class, 'generatePersonalCoupon'])->name('crm.coupons.generate-personal');
    Route::post('/crm/percoupongen', [CoupounController::class, 'generatePersonalCoupon']);

    // PayU Payment Gateway
    Route::post('/payu/initiate', [PayUController::class, 'initiate'])->name('payu.initiate');
    Route::match(['get', 'post'], '/payu/response', [PayUController::class, 'handleResponse'])->name('payu.response');
    Route::get('/payu/receipt', [PayUController::class, 'receipt'])->name('payu.receipt');
});
