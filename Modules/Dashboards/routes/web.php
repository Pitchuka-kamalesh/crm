<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboards\Http\Controllers\DashboardsController;

Route::middleware(['auth'])->group(function () {
    Route::post('dashboards/save-step', [DashboardsController::class, 'saveStep'])->name('dashboards.save-step');
    Route::post('dashboards/save-education', [DashboardsController::class, 'saveEducation'])->name('dashboards.save-education');
    Route::post('dashboards/autosave', [DashboardsController::class, 'autosave'])->name('dashboards.autosave');
    Route::resource('dashboards', DashboardsController::class)->names('dashboards');
});
