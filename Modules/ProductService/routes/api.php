<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductService\Http\Controllers\ProductServiceController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('product-services', ProductServiceController::class)->names('product-services');
});
