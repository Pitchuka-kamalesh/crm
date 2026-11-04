<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboards\Http\Controllers\DashboardsController;

Route::middleware(['auth'])->group(function () {
    Route::post('dashboards/save-education', [DashboardsController::class, 'saveEducation'])->name('dashboards.save-education');
    Route::resource('dashboards', DashboardsController::class)->names('dashboards');
});
