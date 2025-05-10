<?php

use Illuminate\Support\Facades\Route;
use Modules\Clients\Http\Controllers\ClientController;

Route::apiResource('clients', ClientController::class)->names('clients');
    