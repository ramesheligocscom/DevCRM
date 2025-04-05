<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadsController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('leads', LeadsController::class)->names('leads');
    Route::get('/leads/{lead}/clients', [LeadsController::class, 'clientsByLead']);

});
