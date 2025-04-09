<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductService\Http\Controllers\ProductServiceController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('productservice', ProductServiceController::class)->names('productservice');
});
