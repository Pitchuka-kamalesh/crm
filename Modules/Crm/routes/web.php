<?php

use Illuminate\Support\Facades\Route;
use Modules\Crm\Http\Controllers\CrmController;

Route::middleware(['auth'])->group(function () {
    // CRM Navigation & Enquiries
    Route::get('/crm', [CrmController::class, 'index'])->name('crm.alias');
    Route::get('/crms', [CrmController::class, 'index'])->name('crm.index');
    Route::get('/crm/index', [CrmController::class, 'index']);
    Route::get('/crm/stddata', [CrmController::class, 'index'])->name('crm.stddata');
    Route::get('/crm/enquiry-list', [CrmController::class, 'index'])->name('crm.enquirylist');
    Route::get('/crm/stdsearch', [CrmController::class, 'index'])->name('crm.stdsearch');
    Route::get('/crm/register-list', [CrmController::class, 'index'])->name('crm.registerlist');
    Route::get('/crm/crmdatatracking', [CrmController::class, 'index'])->name('crm.crmdatatracking');
    Route::get('/crm/datatracking', [CrmController::class, 'index']);
    Route::get('/crm/employee-list', [CrmController::class, 'index'])->name('crm.employeelist');
    Route::get('/crm/employeelist', [CrmController::class, 'index']);
    Route::post('/crm/allot-leads', [CrmController::class, 'allotLeads'])->name('crm.allot');

    // Coupon Navigation in CRM
    Route::get('/crm/coupon-info', [CrmController::class, 'index'])->name('crm.couponinfo');
    Route::get('/crm/couponinfo', [CrmController::class, 'index']);
    Route::get('/coupoungenration', [CrmController::class, 'index'])->name('crm.coupoungenration');
    Route::get('/crm/coupoungenration', [CrmController::class, 'index']);
    Route::get('/crm/percoupongen', [CrmController::class, 'index'])->name('crm.percoupongen');
    Route::get('/percoupongen', [CrmController::class, 'index']);

    // KPI & Analytics Dashboards
    Route::get('/kpi', [CrmController::class, 'index'])->name('kpi.index');
    Route::get('/kpi/index', [CrmController::class, 'index']);
    Route::get('/kpi/studentkpi', [CrmController::class, 'index'])->name('kpi.studentkpi');
    Route::get('/kpi/telecallerkpi', [CrmController::class, 'index'])->name('kpi.telecallerkpi');
    Route::get('/kpi/leadskpi', [CrmController::class, 'index'])->name('kpi.leadskpi');
});
