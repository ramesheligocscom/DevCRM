<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\DashboardController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('dashboard', DashboardController::class)->names('dashboard');
});
