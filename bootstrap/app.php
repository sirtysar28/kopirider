<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Public booking-flow endpoints are lead-capture only (no authenticated
        // session state), and leads must never be lost to an expired CSRF token
        // — so they are excluded from CSRF verification on purpose.
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // When an already-authenticated staff member opens /login, send them
        // straight to the dashboard instead of silently bouncing to /.
        // (Laravel's default is route('home') — that made /login "redirect
        // back to the homepage" for anyone with a live admin session.)
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
