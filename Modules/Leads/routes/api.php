<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('leads', LeadsController::class)->names('leads');
});
