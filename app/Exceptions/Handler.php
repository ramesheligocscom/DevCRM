<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($e instanceof AuthenticationException) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        });
    }

    /**
     * Handle unauthenticated requests and return JSON response.
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
