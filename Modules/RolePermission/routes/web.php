<?php

use Illuminate\Support\Facades\Route;
use Modules\RolePermission\Http\Controllers\RolePermissionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('rolepermission', RolePermissionController::class)->names('rolepermission');
});
