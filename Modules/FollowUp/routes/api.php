<?php

use Illuminate\Support\Facades\Route;
use Modules\FollowUp\Http\Controllers\FollowUpController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('followup', FollowUpController::class)->names('followup');
});
