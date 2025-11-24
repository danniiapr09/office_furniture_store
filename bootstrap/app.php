<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // route API
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // === SANCTUM & API ===
        $middleware->statefulApi();

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // === CORS ENABLE ===
        $middleware->cors([
            'paths' => ['api/*', 'sanctum/csrf-cookie'],
            'allowed_methods' => ['*'],
            'allowed_origins' => ['*'],  // sementara bebas dulu
            'allowed_headers' => ['*'],
            'exposed_headers' => [],
            'max_age' => 0,
            'supports_credentials' => true,
        ]);

        // === ALIAS MIDDLEWARE ===
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // === CSRF EXCEPTIONS ===
        $middleware->validateCsrfTokens(except: [
            '/api/auth/register',
            '/api/auth/login',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();