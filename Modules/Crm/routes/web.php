<?php

use Illuminate\Support\Facades\Route;
use Modules\Crm\Http\Controllers\CrmController;
use Modules\Crm\Http\Controllers\StudentKpiController;
use Modules\Crm\Http\Controllers\TelecallerKpiController;
use Modules\Crm\Http\Controllers\LeadsKpiController;
use Modules\Crm\Http\Controllers\DiscountCouponController;
use Modules\Crm\Http\Controllers\ErrorKpiController;

Route::middleware(['auth', 'role:1,2,3'])->group(function () {
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
    Route::get('/discountcoupon', [DiscountCouponController::class, 'index'])->name('crm.discountcoupon');
    Route::get('/crm/discountcoupon', [DiscountCouponController::class, 'index']);
    Route::post('/crm/discountcoupon/store', [DiscountCouponController::class, 'store'])->name('crm.discountcoupon.store');
    Route::post('/crm/discountcoupon/stop/{id}', [DiscountCouponController::class, 'stop'])->name('crm.discountcoupon.stop');
    Route::post('/crm/discountcoupon/activate/{id}', [DiscountCouponController::class, 'activate'])->name('crm.discountcoupon.activate');

    // KPI & Analytics Dashboards
    Route::get('/kpi', [StudentKpiController::class, 'index'])->name('kpi.index');
    Route::get('/kpi/index', [StudentKpiController::class, 'index']);
    Route::get('/kpi/studentkpi', [StudentKpiController::class, 'index'])->name('kpi.studentkpi');
    Route::get('/kpi/studentkpi/export', [StudentKpiController::class, 'export'])->name('kpi.studentkpi.export');
    Route::get('/kpi/telecallerkpi', [TelecallerKpiController::class, 'index'])->name('kpi.telecallerkpi');
    Route::get('/kpi/leadskpi', [LeadsKpiController::class, 'index'])->name('kpi.leadskpi');
    Route::get('/kpi/errors', [ErrorKpiController::class, 'index'])->name('kpi.errors');
    Route::get('/kpi/errors/export', [ErrorKpiController::class, 'export'])->name('kpi.errors.export');
    Route::post('/kpi/errors/clear', [ErrorKpiController::class, 'clearLogs'])->name('kpi.errors.clear');
});