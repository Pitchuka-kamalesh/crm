<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboards\Http\Controllers\DashboardsController;
use Modules\Dashboards\Http\Controllers\RegistrationController;
use Modules\Dashboards\Http\Controllers\ApplicationController;
use Modules\Dashboards\Http\Controllers\ExamController;

// Public Registration Routes
Route::get('/register', [RegistrationController::class, 'show'])->name('register');
Route::post('/register', [RegistrationController::class, 'store'])->name('register.post');
Route::post('/register/autosave', [RegistrationController::class, 'autosave'])->name('register.autosave');
Route::get('/api/programs/{id}/branches', [RegistrationController::class, 'getBranches'])->name('api.programs.branches');
Route::get('/api/districts/{stateId}', [RegistrationController::class, 'getDistricts'])->name('api.districts.by-state');

Route::middleware(['auth'])->group(function () {
    // Multi-step Student Onboarding
    Route::post('dashboards/save-step', [DashboardsController::class, 'saveStep'])->name('dashboards.save-step');
    Route::post('dashboards/save-education', [DashboardsController::class, 'saveEducation'])->name('dashboards.save-education');
    Route::post('dashboards/autosave', [DashboardsController::class, 'autosave'])->name('dashboards.autosave');
    Route::post('/dashboards/apply-coupon', [DashboardsController::class, 'applyPersonalCoupon'])->name('dashboards.apply-coupon');
    Route::post('/dashboards/remove-coupon', [DashboardsController::class, 'removePersonalCoupon'])->name('dashboards.remove-coupon');
    Route::resource('dashboards', DashboardsController::class)->names('dashboards');

    // Step aliases matching master.menus
    Route::get('/step1', fn() => redirect()->route('dashboards.index', ['step' => 1]))->name('steps.step1');
    Route::get('/step2', fn() => redirect()->route('dashboards.index', ['step' => 2]))->name('steps.step2');
    Route::get('/step3', fn() => redirect()->route('dashboards.index', ['step' => 3]))->name('steps.step3');
    Route::get('/step4', fn() => redirect()->route('dashboards.index', ['step' => 4]))->name('steps.step4');
    Route::get('/step5', fn() => redirect()->route('dashboards.index', ['step' => 5]))->name('steps.step5');
    Route::get('/step6', fn() => redirect()->route('dashboards.index', ['step' => 6]))->name('steps.step6');
    Route::get('/step7', fn() => redirect()->route('dashboards.index', ['step' => 7]))->name('steps.step7');
    Route::get('/step8', fn() => redirect()->route('dashboards.index', ['step' => 8]))->name('steps.step8');
    Route::get('/finance/payfee', fn() => redirect()->route('dashboards.index', ['step' => 1]));

    // Exam Slot Booking Tracking
    Route::get('/exam/examslotdata', [ExamController::class, 'slotData'])->name('exam.examslotdata');
    Route::get('/exam/index', [ExamController::class, 'slotData'])->name('exam.index');
    Route::get('/exam', [ExamController::class, 'slotData']);
    Route::get('/exams', [ExamController::class, 'slotData']);
    Route::get('/examslotdata', [ExamController::class, 'slotData']);

    // Official A4 Application Form Preview & PDF Download
    Route::get('/application/{id}', [ApplicationController::class, 'show'])->name('application.show');
    Route::get('/application/{id}/download', [ApplicationController::class, 'download'])->name('application.download');
});
