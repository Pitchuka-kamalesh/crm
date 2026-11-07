<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadsController;

Route::middleware(['auth'])->group(function () {
    Route::post('leads/{applinano}/update-stage', [LeadsController::class, 'updateStage'])->name('leads.updateStage');
    Route::post('leads/{applinano}/update-status', [LeadsController::class, 'updateStatus'])->name('leads.updateStatus');
    Route::resource('leads', LeadsController::class)->names('leads');
});
