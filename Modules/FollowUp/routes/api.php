<?php

use Illuminate\Support\Facades\Route;
use Modules\FollowUp\Http\Controllers\FollowUpController;
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('followup', FollowUpController::class)->names('followup');
});
