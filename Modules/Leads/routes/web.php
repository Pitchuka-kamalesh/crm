<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadsController;

Route::middleware(['auth'])->group(function () {
    Route::get('/leads', [LeadsController::class, 'index'])->name('leads.overview');
    Route::get('/leads/index', [LeadsController::class, 'index'])->name('leads.index');
    Route::get('/leads/allottedleads', [LeadsController::class, 'index'])->name('leads.allotted');
    Route::get('/leads/leadsallocation', [LeadsController::class, 'index'])->name('leads.allocation');
    Route::get('/leads/leadsupload', [LeadsController::class, 'index'])->name('leads.upload');
    Route::post('/leads/upload-excel', [LeadsController::class, 'uploadExcel'])->name('leads.uploadExcel');
    Route::get('/leads/download-sample', [LeadsController::class, 'downloadSample'])->name('leads.downloadSample');
    
    Route::get('/leads/leads input', [LeadsController::class, 'index'])->name('leads.input');
    Route::get('/leads/leadsinput', [LeadsController::class, 'index']);
    Route::get('/leads/leads-input', [LeadsController::class, 'index']);
    Route::post('/leads/store-input', [LeadsController::class, 'storeLeadInput'])->name('leads.storeInput');

    Route::post('/leads/allocate', [LeadsController::class, 'allocate'])->name('leads.allocate');
    Route::post('/leads/log-task', [LeadsController::class, 'logTask'])->name('leads.logTask');
    Route::post('/leads/{applinano}/update-stage', [LeadsController::class, 'updateStage'])->name('leads.updateStage');
    Route::post('/leads/{applinano}/update-status', [LeadsController::class, 'updateStatus'])->name('leads.updateStatus');
    Route::get('/leads/{applinano}/history', [LeadsController::class, 'taskHistory'])->name('leads.taskHistory');
});
