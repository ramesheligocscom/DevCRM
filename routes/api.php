<?php
// routes/api.php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TableHeaderManageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'signIn']);
Route::post('/log-unauthenticated-access', [AuthController::class, 'logUnauthenticatedAccess']);
Route::get('/dropdown-user-list', [UserController::class, 'dropdownUserList'])->name('dropdown.user.list');

Route::middleware(['auth:sanctum'])->group(function () {
    # Table Header Manage Api
    Route::group(['prefix' => 'table-header'], function () {
        Route::get('/get', [TableHeaderManageController::class, 'getTableHeaders']);
        Route::post('/save', [TableHeaderManageController::class, 'saveTableHeaders']);
        Route::post('/sync', [TableHeaderManageController::class, 'tableHeaderSync']);
    });
});
