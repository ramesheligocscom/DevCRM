<?php

use Illuminate\Support\Facades\Route;
use Modules\SiteVisit\Http\Controllers\SiteVisitController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('sitevisit', SiteVisitController::class)->names('sitevisit');
});
