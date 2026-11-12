<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// Landing & Login Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/login/send-otp', [LoginController::class, 'sendOtp'])->name('login.send-otp');
Route::get('/demo-login/{usercode}', [LoginController::class, 'demoLogin'])->name('demo.login');
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Official Admission Registration Routes (Matching admissions.vignan.ac.in)
Route::get('/register', [RegistrationController::class, 'show'])->name('register');
Route::post('/register', [RegistrationController::class, 'store'])->name('register.post');
Route::post('/register/autosave', [RegistrationController::class, 'autosave'])->name('register.autosave');
Route::get('/api/programs/{id}/branches', [RegistrationController::class, 'getBranches'])->name('api.programs.branches');
Route::get('/api/districts/{stateId}', [RegistrationController::class, 'getDistricts'])->name('api.districts.by-state');

// Dashboard shortcut & Role Switching
Route::get('/dashboard', function () {
    return redirect(app(LoginController::class)->getLandingUrlForUser(auth()->user()));
})->middleware('auth')->name('dashboard');

Route::post('/switch-role', [LoginController::class, 'switchRole'])->middleware('auth')->name('role.switch');

// Direct CRM aliases matching master.menus controller_ref
Route::get('/crm', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.alias');
Route::get('/crm/index', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth');
Route::get('/crm/stddata', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.stddata');
Route::get('/crm/enquiry-list', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.enquirylist');
Route::get('/crm/stdsearch', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.stdsearch');
Route::get('/crm/register-list', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.registerlist');
Route::get('/crm/coupon-info', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.couponinfo');
Route::get('/crm/couponinfo', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth');
Route::get('/coupoungenration', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.coupoungenration');
Route::get('/crm/coupoungenration', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth');
Route::get('/crm/employee-list', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.employeelist');
Route::get('/crm/employeelist', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth');
Route::post('/crm/allot-leads', [\Modules\Crm\Http\Controllers\CrmController::class, 'allotLeads'])->middleware('auth')->name('crm.allot');
Route::post('/crm/coupons/generate-personal', [\Modules\Crm\Http\Controllers\CrmController::class, 'generatePersonalCoupon'])->middleware('auth')->name('crm.coupons.generate-personal');

// Leads aliases matching master.menus controller_ref
Route::get('/leads/index', [\Modules\Leads\Http\Controllers\LeadsController::class, 'index'])->middleware('auth');
Route::get('/leads/allottedleads', [\Modules\Leads\Http\Controllers\LeadsController::class, 'index'])->middleware('auth')->name('leads.allotted');
Route::get('/leads/leadsallocation', [\Modules\Leads\Http\Controllers\LeadsController::class, 'index'])->middleware('auth')->name('leads.allocation');
Route::get('/leads/leads input', [\Modules\Leads\Http\Controllers\LeadsController::class, 'index'])->middleware('auth')->name('leads.input');
Route::get('/leads/leadsinput', [\Modules\Leads\Http\Controllers\LeadsController::class, 'index'])->middleware('auth');
Route::get('/leads/leads-input', [\Modules\Leads\Http\Controllers\LeadsController::class, 'index'])->middleware('auth');
Route::post('/leads/store-input', [\Modules\Leads\Http\Controllers\LeadsController::class, 'storeLeadInput'])->middleware('auth')->name('leads.storeInput');
Route::post('/leads/allocate', [\Modules\Leads\Http\Controllers\LeadsController::class, 'allocate'])->middleware('auth');
Route::post('/leads/log-task', [\Modules\Leads\Http\Controllers\LeadsController::class, 'logTask'])->middleware('auth');

// Finance aliases matching master.menus controller_ref
Route::get('/finance', [\Modules\Finance\Http\Controllers\FinanceController::class, 'index'])->middleware('auth')->name('finance.index');
Route::get('/finance/index', [\Modules\Finance\Http\Controllers\FinanceController::class, 'index'])->middleware('auth');
Route::get('/finance/paiddetails', [\Modules\Finance\Http\Controllers\FinanceController::class, 'index'])->middleware('auth')->name('finance.paiddetails');

// KPI aliases matching master.menus controller_ref
Route::get('/kpi', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('kpi.index');
Route::get('/kpi/index', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth');
Route::get('/kpi/studentkpi', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('kpi.studentkpi');

// Student Admission Onboarding Step aliases matching master.menus controller_ref
Route::get('/step1', fn() => redirect()->route('dashboards.index', ['step' => 1]))->middleware('auth')->name('steps.step1');
Route::get('/step2', fn() => redirect()->route('dashboards.index', ['step' => 2]))->middleware('auth')->name('steps.step2');
Route::get('/step3', fn() => redirect()->route('dashboards.index', ['step' => 3]))->middleware('auth')->name('steps.step3');
Route::get('/step4', fn() => redirect()->route('dashboards.index', ['step' => 4]))->middleware('auth')->name('steps.step4');
Route::get('/step5', fn() => redirect()->route('dashboards.index', ['step' => 5]))->middleware('auth')->name('steps.step5');
Route::get('/crm/stpstep2', fn() => redirect()->route('dashboards.index', ['step' => 2]))->middleware('auth');
Route::get('/crm/stpstep3', fn() => redirect()->route('dashboards.index', ['step' => 3]))->middleware('auth');
Route::get('/crm/stpstep4', fn() => redirect()->route('dashboards.index', ['step' => 4]))->middleware('auth');
Route::get('/crm/stpstep5', fn() => redirect()->route('dashboards.index', ['step' => 5]))->middleware('auth');
Route::get('/crm/stpstep6', fn() => redirect()->route('dashboards.index', ['step' => 6]))->middleware('auth');
Route::get('/crm/stpstep7', fn() => redirect()->route('dashboards.index', ['step' => 7]))->middleware('auth');
Route::get('/finance/payfee', fn() => redirect()->route('dashboards.index', ['step' => 1]))->middleware('auth');

// Student Personal Coupon Application on Stage 1
Route::post('/dashboards/apply-coupon', [\Modules\Dashboards\Http\Controllers\DashboardsController::class, 'applyPersonalCoupon'])->middleware('auth')->name('dashboards.apply-coupon');
Route::post('/dashboards/remove-coupon', [\Modules\Dashboards\Http\Controllers\DashboardsController::class, 'removePersonalCoupon'])->middleware('auth')->name('dashboards.remove-coupon');

// PayU Gateway Integration Routes
Route::post('/payu/initiate', [\App\Http\Controllers\PayUController::class, 'initiate'])->middleware('auth')->name('payu.initiate');
Route::match(['get', 'post'], '/payu/response', [\App\Http\Controllers\PayUController::class, 'handleResponse'])->name('payu.response');
Route::get('/payu/receipt', [\App\Http\Controllers\PayUController::class, 'receipt'])->middleware('auth')->name('payu.receipt');

// Official A4 Application Form Preview & PDF Download
Route::get('/application/{id}', [\App\Http\Controllers\ApplicationController::class, 'show'])->middleware('auth')->name('application.show');
Route::get('/application/{id}/download', [\App\Http\Controllers\ApplicationController::class, 'download'])->middleware('auth')->name('application.download');

