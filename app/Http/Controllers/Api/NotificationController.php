<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationMessage;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\RolePermission\Constants\RolePermissionConst;

class NotificationController extends Controller
{

    const CONTROLLER_NAME = "Notification Controller";

    protected $referer;
    protected $login_user;

    public function __construct()
    {
        $this->referer = request()->header('referer');
        $this->login_user = request()->user() ?? null;
    }

    public function testMessageSendPusher(Request $request)
    {
        try {
            $message = "You have a new notification!";
            $user_id = $this->login_user ? $this->login_user->uuid : null;
            event(new NotificationMessage($message, $user_id));
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
        return $this->actionSuccess('You have a new notification');
    }

    public function getNotificationCount(Request $request)
    {
        try {
            $user = $this->login_user;
            $roleSlugs = $user->roles()->pluck('slug')->toArray();

            # Base query with user filtering
            $query = Notification::query();
            if (!array_intersect($roleSlugs, [RolePermissionConst::SLUG_SUPER_ADMIN, RolePermissionConst::SLUG_ADMIN])) {
                $query->where('show_ids', 'LIKE', '%' . $user->uuid . '%');
            }

            # Get total notifications count
            $total = $query->count();

            # Get read notifications count
            $query_1 = NotificationUser::query()->where('user_id', $this->login_user->uuid);
            $read_count = $query_1->where('is_read', true)
                ->whereIn('notification_id', $query->pluck('id')) # Get only relevant notifications
                ->count();

            # Calculate unread count
            $read = [
                'total' => $total,
                'read_count' => $read_count,
                'un_read' => ($total - $read_count) > 0 ? $total - $read_count : 0,
            ];

            return $this->actionSuccess("Notification count retrieved successfully.", $read);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function getLatestFiveNotificationList(Request $request)
    {
        try {
            $query = Notification::query()->search($request)->with(['creator:id,name', 'lead:id,name', 'client:id,name', 'read' => function ($que) {
                $que->where('user_id', $this->login_user->uuid);
            }]);

            $user = $this->login_user;
            $roleSlugs = $user->roles()->pluck('slug')->toArray();
            if (!array_intersect($roleSlugs, [RolePermissionConst::SLUG_SUPER_ADMIN, RolePermissionConst::SLUG_ADMIN])) {
                $query->where('show_ids', 'LIKE', '%' . $user->uuid . '%');
            }

            $list = $query->latest()->limit(5)->get();
            return $this->actionSuccess("Notification Latest Five list retrieved successfully.", $list);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function getNotificationList(Request $request)
    {
        try {
            $sortKey = $request->sort_key ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';
            $startDate = $request->startDate ?? null;
            $endDate = $request->endDate ?? null;
            $user = $this->login_user;
            $roleSlugs = $user->roles()->pluck('slug')->toArray();

            $query = Notification::query()->search($request)->with(['creator:id,name', 'lead:id,name', 'client:id,name', 'read' => function ($que) {
                $que->where('user_id', $this->login_user->uuid);
            }]);

            if (!array_intersect($roleSlugs, [RolePermissionConst::SLUG_SUPER_ADMIN, RolePermissionConst::SLUG_ADMIN])) {
                $query->where('show_ids', 'LIKE', '%' . $user->uuid . '%');
            }

            $query->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            })->when($sortKey, function ($query) use ($sortKey, $sortOrder) {
                $query->orderBy($sortKey, $sortOrder);
            });

            $list = $query->latest()->paginate($request->per_page ?? 10);

            return $this->actionSuccess("Notification list retrieved successfully.", customizingResponseData($list));
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function isReadNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
            'is_read' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }
        DB::beginTransaction();
        try {
            $notificationUser = NotificationUser::where('notification_id', $request->notification_id)->where('user_id', $request->user_id ? $request->user_id : Auth::id())->first();
            if (!$notificationUser) {
                $notificationUser = new NotificationUser();
                $notificationUser->notification_id = $request->notification_id;
                $notificationUser->user_id = $request->user_id ? $request->user_id : Auth::user()->uuid;
            }
            $notificationUser->is_read = $request->is_read;
            $notificationUser->save();
            DB::commit();
            return $this->actionSuccess("Notification is read Update successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function markAllReadOrUnread(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_read' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $user = $this->login_user;
            $roleSlugs = $user->roles()->pluck('slug')->toArray();

            $query = Notification::query();
            if (!array_intersect($roleSlugs, [RolePermissionConst::SLUG_SUPER_ADMIN, RolePermissionConst::SLUG_ADMIN])) {
                $query->where('show_ids', 'LIKE', '%' . $user->uuid . '%');
            }

            if ($request->is_read) {
                $read_ids = NotificationUser::where('user_id', $user->uuid)->where('is_read', false)->pluck('notification_id')->toArray();
            } else {
                $read_ids = NotificationUser::where('user_id', $user->uuid)->where('is_read', true)->pluck('notification_id')->toArray();
            }

            $notificationIds = $query->whereIn('id', $read_ids)->pluck('id')->toArray();

            foreach ($notificationIds as $key => $notification_id) {
                $notificationUser = NotificationUser::where('notification_id', $notification_id)->where('user_id', $user->uuid)->first();
                if (!$notificationUser) {
                    $notificationUser = new NotificationUser();
                    $notificationUser->notification_id = $notification_id;
                    $notificationUser->user_id = $user->uuid;
                }
                $notificationUser->is_read = $request->is_read;
                $notificationUser->save();
            }

            DB::commit();
            return $this->actionSuccess("Notification status updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function deleteNotification(Request $request)
    {
        $validator = Validator::make(['action' => $request->action, 'notification_id' => $request->notification_id], [
            'notification_id' => 'required|exists:notifications,id',
            'delete_text' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $notification_id = $request->notification_id;

            $notification = Notification::findOrFail($notification_id);

            if (strtolower(trim($request->delete_text)) !== 'delete') {
                return $this->actionFailure('Your Delete input value is wrong. If you are permanently deleting the file, please type "DELETE" to confirm!');
            }
            $notification->Delete();
            $message = 'Notification permanently deleted successfully.';

            DB::commit();
            return $this->actionSuccess($message);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }
}
