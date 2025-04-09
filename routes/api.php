<?php
// routes/api.php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\TableHeaderManageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'signIn']);
Route::post('/log-unauthenticated-access', [AuthController::class, 'logUnauthenticatedAccess']);
Route::get('/dropdown-user-list', [UserController::class, 'dropdownUserList'])->name('dropdown.user.list');
Route::post('/update-password/{user_id}', [UserController::class, 'updatePassword'])->name('update-password');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [AuthController::class, 'getProfile']);
    # User api Name
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::get('/user-activity-timeline/{user_id}', [UserController::class, 'getUserActivityTimelineList'])->name('user.activity.timeline');
        Route::get('/{user_id}', [UserController::class, 'show'])->name('user.show');
        Route::post('/option-list', [UserController::class, 'userOptionList'])->name('user.option.list');
        Route::post('/status-update', [UserController::class, 'userStatusUpdate'])->name('user.status.update');
        Route::post('/role-update', [UserController::class, 'userRoleUpdate'])->name('user.role.update');
        Route::post('/create', [UserController::class, 'store'])->name('user.create');
        Route::post('/update', [UserController::class, 'update'])->name('user.update');
        Route::delete('/{user_id}', [UserController::class, 'destroy'])->name('user.delete');
    });

    # Table Header Manage Api
    Route::group(['prefix' => 'table-header'], function () {
        Route::get('/get', [TableHeaderManageController::class, 'getTableHeaders']);
        Route::post('/save', [TableHeaderManageController::class, 'saveTableHeaders']);
        Route::post('/sync', [TableHeaderManageController::class, 'tableHeaderSync']);
    });
});
