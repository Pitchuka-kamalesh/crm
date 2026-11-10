<?php

use Illuminate\Support\Facades\Route;
use Modules\Finance\Http\Controllers\FinanceController;

Route::middleware(['auth'])->group(function () {
    Route::resource('finances', FinanceController::class)->names('finance');
});
