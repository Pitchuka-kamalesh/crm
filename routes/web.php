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
Route::get('/crm/stdsearch', [\Modules\Crm\Http\Controllers\CrmController::class, 'index'])->middleware('auth')->name('crm.stdsearch');
