<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Http\Controllers\InvoiceController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('invoices', InvoiceController::class)->names('invoices');
});
