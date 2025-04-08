<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('leads', LeadController::class)->names('leads');
});
