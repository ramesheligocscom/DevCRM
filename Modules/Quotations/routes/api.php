<?php

use Illuminate\Support\Facades\Route;
use Modules\Quotations\Http\Controllers\QuotationController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('quotations', QuotationController::class)->names('quotations');
});
