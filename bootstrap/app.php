<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
->withRouting(
    web: array_merge(
        [__DIR__.'/../routes/web.php'],
        glob(base_path('Modules/*/routes/web.php')) ?: []
    ),
    api: array_merge(
        glob(base_path('Modules/*/routes/api.php')) ?: []
    ),
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
