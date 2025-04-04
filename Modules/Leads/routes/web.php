<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('leads', LeadsController::class)->names('leads');
});
