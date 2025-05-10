<?php

use Illuminate\Support\Facades\Route;
use Modules\SiteVisit\Http\Controllers\SiteVisitController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('sitevisit', SiteVisitController::class)->names('sitevisit');
    Route::patch('sitevisit/{id}/status', [SiteVisitController::class, 'status'])->name('sitevisit.status');
});
