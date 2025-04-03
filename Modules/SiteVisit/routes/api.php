<?php

use Illuminate\Support\Facades\Route;
use Modules\SiteVisit\Http\Controllers\SiteVisitController;

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
Route::apiResource('sitevisit', SiteVisitController::class)->names('sitevisit');
// });
