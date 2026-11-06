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
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Official Admission Registration Routes (Matching admissions.vignan.ac.in)
Route::get('/register', [RegistrationController::class, 'show'])->name('register');
Route::post('/register', [RegistrationController::class, 'store'])->name('register.post');
Route::get('/api/programs/{id}/branches', [RegistrationController::class, 'getBranches'])->name('api.programs.branches');

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
Route::get('/crm/employee-list', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.employeelist');
Route::get('/crm/employeelist', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth');
Route::post('/crm/allot-leads', [\Modules\Crm\Http\Controllers\CrmController::class, 'allotLeads'])->middleware('auth')->name('crm.allot');

// Leads aliases matching master.menus controller_ref
Route::get('/leads/index', [\Modules\Leads\Http\Controllers\LeadsController::class, 'index'])->middleware('auth');
Route::get('/leads/allottedleads', [\Modules\Leads\Http\Controllers\LeadsController::class, 'index'])->middleware('auth')->name('leads.allotted');
Route::get('/leads/leadsallocation', [\Modules\Leads\Http\Controllers\LeadsController::class, 'index'])->middleware('auth')->name('leads.allocation');

// Finance aliases matching master.menus controller_ref
Route::get('/finance/index', [\Modules\Finance\Http\Controllers\FinanceController::class, 'index'])->middleware('auth');
Route::get('/finance/paiddetails', [\Modules\Finance\Http\Controllers\FinanceController::class, 'index'])->middleware('auth')->name('finance.paiddetails');

// KPI aliases matching master.menus controller_ref
Route::get('/kpi', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('kpi.index');
Route::get('/kpi/index', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth');
Route::get('/kpi/studentkpi', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('kpi.studentkpi');
