<?php

use Illuminate\Support\Facades\Route;
use Modules\Contracts\Http\Controllers\ContractController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('contracts', ContractController::class)->names('contracts');
});
