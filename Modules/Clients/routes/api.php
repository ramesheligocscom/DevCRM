<?php

use Illuminate\Support\Facades\Route;
use Modules\Clients\Http\Controllers\ClientController;

Route::get('/api/v1/test', function () {
    return response()->json(['status' => 'working']);
});
