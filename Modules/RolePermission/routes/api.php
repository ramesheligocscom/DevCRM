<?php

use Illuminate\Support\Facades\Route;
use Modules\RolePermission\Http\Controllers\RolePermissionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::group(['prefix' => 'role'], function () {
        # Role api Name
        Route::get('/', [RolePermissionController::class, 'getRoleList'])->name('role.index');
        Route::get('/rank/target', [RolePermissionController::class, 'getTargetRankList'])->name('role.rank.target');
        Route::get('/duplicate/{role_id}', [RolePermissionController::class, 'duplicateRoleCreate'])->name('role.index');
        Route::post('/info', [RolePermissionController::class, 'getRoleInfo'])->name('role.info');
        Route::post('/create', [RolePermissionController::class, 'saveRole'])->name('role.create');
        Route::post('/update', [RolePermissionController::class, 'saveRole'])->name('role.update');
        Route::get('/legal', [RolePermissionController::class, 'getLegalRole'])->name('role.legal');
        Route::get('/roll-assign-permission-list', [RolePermissionController::class, 'rollAssignPermissionList'])->name('role.assign.permission');
        Route::delete('/{role_id}', [RolePermissionController::class, 'roleDelete'])->name('role.delete');
        Route::get('/user-permission', [RolePermissionController::class, 'userPermission'])->name('user.permission');
    });
    # Permission api Name
    Route::group(['prefix' => 'permission'], function () {
        Route::get('/', [RolePermissionController::class, 'getPermissionList'])->name('permission.index');
    });
});
