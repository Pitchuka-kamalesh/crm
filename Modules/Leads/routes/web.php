<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadsController;

Route::middleware(['auth'])->group(function () {
    Route::post('leads/allocate', [LeadsController::class, 'allocate'])->name('leads.allocate');
    Route::post('leads/log-task', [LeadsController::class, 'logTask'])->name('leads.logTask');
    Route::post('leads/{applinano}/update-stage', [LeadsController::class, 'updateStage'])->name('leads.updateStage');
    Route::post('leads/{applinano}/update-status', [LeadsController::class, 'updateStatus'])->name('leads.updateStatus');
    Route::get('leads/{applinano}/history', [LeadsController::class, 'taskHistory'])->name('leads.taskHistory');
    Route::resource('leads', LeadsController::class)->names('leads');
});
