<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: array_merge(
            [__DIR__ . '/../routes/web.php'],
            glob(base_path('Modules/*/routes/web.php')) ?: []
        ),
        api: array_merge(
            [__DIR__ . '/../routes/api.php'], // 👈 add main api.php route
            glob(base_path('Modules/*/routes/api.php')) ?: []
        ),
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Handler $e) {});
    })->create();
