<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authenticate;

use Modules\Auth\Http\Middleware\CheckRole;
use App\Http\Controllers\PayUController;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'

        // then: function () {
        //     Route::post('/payu/response', [
        //         PayUController::class,
        //         'handleResponse'
        //     ])
        //     ->withoutMiddleware([
        //         Authenticate::class,
        //         CheckRole::class,
        //     ])
        //     ->name('payu.response');
        // },
    )

    ->withMiddleware(function (Middleware $middleware): void {

        // Register role middleware
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        // Exclude external payment callback from CSRF
        $middleware->validateCsrfTokens(except: [
            'payu/response',
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*') ||
                $request->expectsJson(),
        );

    })

    ->create();
