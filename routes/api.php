<?php
// routes/api.php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TableHeaderManageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'signIn']);
Route::post('/log-unauthenticated-access', [AuthController::class, 'logUnauthenticatedAccess']);
Route::get('/dropdown-user-list', [UserController::class, 'dropdownUserList'])->name('dropdown.user.list');
Route::post('/update-password/{user_id}', [UserController::class, 'updatePassword'])->name('update-password');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::get('/logout', [AuthController::class, 'logout']);
    # User Login Log api
    Route::get('/user-login-logs', [AuthController::class, 'getUserLoginLogs']);
    Route::delete('/delete-login-log/{login_log_id}', [AuthController::class, 'deleteLoginLog'])->name('delete.login.log');

    # Notification Api
    Route::post('/notification-count', [NotificationController::class, 'getNotificationCount']);
    Route::post('/latest-five-notification-list', [NotificationController::class, 'getLatestFiveNotificationList']);
    Route::post('/notification-list', [NotificationController::class, 'getNotificationList']);
    Route::post('/is-read-notification', [NotificationController::class, 'isReadNotification']);
    Route::post('/mark-all-read-or-un-read', [NotificationController::class, 'markAllReadOrUnread']);
    Route::delete('/delete-notification/{notification_id}', [NotificationController::class, 'deleteNotification']);

    Route::post('/test-message-send-pusher', [NotificationController::class, 'testMessageSendPusher']);

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

    # Settings api Name
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/', [SettingController::class, 'update'])->name('settings.update');
        # For status setting
        Route::get('/page', [SettingController::class, 'pageList'])->name('settings.pageList');
        Route::get('/status-list', [SettingController::class, 'pageStatusList'])->name('settings.pageStatusList');
        Route::post('/page-status-create', [SettingController::class, 'pageStatusCreate'])->name('settings.pageStatusCreate');
        Route::post('/status-update/{status_id}', [SettingController::class, 'statusUpdate'])->name('settings.statusUpdate');
        Route::post('/change-color-status/{status_id}', [SettingController::class, 'changeColorStatus'])->name('settings.changeColorStatus');
        Route::put('/page-status-update/{status_id}', [SettingController::class, 'pageStatusUpdate'])->name('settings.pageStatusUpdate');
        Route::delete('/page-status-delete/{status_id}', [SettingController::class, 'pageStatusDelete'])->name('settings.pageStatusDelete');
    });
});
