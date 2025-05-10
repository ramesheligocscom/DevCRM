<?php

use Illuminate\Support\Facades\Route;
use Modules\Quotations\Http\Controllers\QuotationController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('quotations', QuotationController::class)->names('quotations');
});
