<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('leads', LeadController::class)->names('leads');
});
