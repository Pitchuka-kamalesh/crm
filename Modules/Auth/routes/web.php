<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\LoginController;

// Landing & Login Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/login/send-otp', [LoginController::class, 'sendOtp'])->name('login.send-otp');
Route::get('/demo-login/{usercode}', [LoginController::class, 'demoLogin'])->name('demo.login');
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard shortcut & Role Switching
Route::get('/dashboard', function () {
    return redirect(app(LoginController::class)->getLandingUrlForUser(auth()->user()));
})->middleware('auth')->name('dashboard');

Route::post('/switch-role', [LoginController::class, 'switchRole'])->middleware('auth')->name('role.switch');
