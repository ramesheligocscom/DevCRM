<?php

use Illuminate\Support\Facades\Route;
use Modules\FollowUp\Http\Controllers\FollowUpController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('followup', FollowUpController::class)->names('followup');
});
