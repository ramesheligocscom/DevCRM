<?php

use App\Constants\ArrayListConst;
use App\Events\SendMessage;
use App\Models\ExceptionLog;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\TableHeaderManage;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Modules\RolePermission\Constants\RolePermissionConst;
use Modules\RolePermission\Models\Permission;

const COMMON_HELPER = 'Helper / Common Helper';

function i($msg)
{
    Log::info($msg);
}

function er($msg)
{
    Log::error($msg);
}

/* It creates an exception error
*
* @param exception The exception object
* @param type This is the type of exception.
*/
function createExceptionError($exception, $type, $function = null)
{
    Log::error("$type : $function => " . $exception->getMessage(), ['exception' => $exception]);
    try {
        $error_message = "Function $function : " . $exception->getMessage();
        $exception_first = ExceptionLog::where('status', ExceptionLog::PENDING)
            ->where('type', $type)->where('error', $error_message)
            ->where('line_number', $exception->getLine())
            ->latest()
            ->first();

        if (!$exception_first) {
            $data = [
                'status' => ExceptionLog::PENDING,
                'type' => $type,
                'title' => get_class($exception),
                'error' => $error_message,
                'file_name' => $exception->getFile(),
                'line_number' => $exception->getLine(),
                'full_error' => $exception,
                'type_count' => 1,
            ];
            ExceptionLog::create($data);
        } else {
            $exception_first->type_count += 1;
            $exception_first->error = "Function $function : " . $exception->getMessage();
            $exception_first->save();
        }
        return true;
    } catch (\Exception $e) {
        Log::error(COMMON_HELPER . " : createExceptionError => " . $e->getMessage(), ['exception' => $e]);
        return false;
    }
}

/**
 * It takes a role, and assigns all permissions to it, except for the ones that are in the array of
 * permissions that are denied
 *
 * @param role The name of the role.
 *
 */
function createNewRole($role)
{
    $permissions_ids = Permission::pluck('id')->toArray();

    # remove move old permission assign in $role  
    switch ($role->slug) {
        case RolePermissionConst::SLUG_SUPER_ADMIN:
            $permissions_ids = Permission::pluck('id')->toArray();
            break;
        case RolePermissionConst::SLUG_ADMIN:
            $permissions_ids = Permission::whereIn('permission', RolePermissionConst::ADMIN_PERMISSION)->pluck('id')->toArray();
            break;
        default:
            $permissions_ids = [];
            break;
    }
    $role->permissions()->sync($permissions_ids);
    return $role;
}

function customizingResponseData($list)
{
    $data = [
        'data' => $list->items(),
        'current_page' => $list->currentPage(),
        'last_page' => $list->lastPage(),
        'per_page' => $list->perPage(),
        'total' => $list->total(),
        'from' => $list->firstItem(),
        'to' => $list->lastItem(),
    ];

    return $data;
}

function getStatusName($status = null)
{
    return match ($status) {
        'no_action' => 'No Action',
        'follow_up' => 'Follow up',
        'interested' => 'Interested',
        'not_interested' => 'Not Interested',
        'ready_for_srm' => 'Ready For SRM',
        'ready_for_quotation' => 'Ready For Quotation',
        'quotation_created' => 'Quotation Created',
        'quotation_draft' => 'Quotation Draft',
        'quotation_in_progress_25' => 'Quotation in progress 25 %',
        'quotation_in_progress_50' => 'Quotation in progress 50 %',
        'quotation_in_progress_75' => 'Quotation in progress 75 %',
        'quotation_accepted' => 'Quotation Accepted',
        'quotation_cancelled' => 'Quotation Cancelled',
        default => null,
    };
}

function createTableHeaderManage($slug)
{
    $list = ArrayListConst::HEADER_MANAGE_LIST ?? [];

    # Find matching entry

    $info = collect($list)->firstWhere(fn($item) => $item['slug'] === $slug);

    if (!$info) return false;

    $header = TableHeaderManage::updateOrCreate(
        [
            'user_id' => Auth::user()->uuid,
            'slug' => $slug,
        ],
        [
            'title' => $info['title'],
            'table' => $info['table'],
            'headers' => json_encode($info['headers'])
        ]
    );

    return $header;
}

function syncAllUserTableHeaderManage($slug)
{
    $list = ArrayListConst::HEADER_MANAGE_LIST ?? [];

    # Find matching entry

    $info = collect($list)->firstWhere(fn($item) => $item['slug'] === $slug);

    if (!$info) return false;

    $header = TableHeaderManage::where('slug', $slug)->update(
        ['title' => $info['title'], 'table' => $info['table'], 'headers' => json_encode($info['headers'])]
    );

    return $header;
}

function addStoragePermission($file_path)
{
    $storagePath = storage_path($file_path);

    # Ensure the directory exists with correct permissions
    if (!file_exists($storagePath)) {
        mkdir($storagePath, 0755, true); # Create the directory recursively
    }
}

function adminUserId()
{
    return User::withTrashed()->whereHas('user_role', function ($qu) {
        $qu->whereHas('role', function ($q) {
            $q->whereIn('slug', [RolePermissionConst::SLUG_SUPER_ADMIN, RolePermissionConst::SLUG_ADMIN]);
        });
    })->pluck('id')->toArray();
}

function statusChangeGoNotification($lead, $quotation, $status)
{
    $ids = adminUserId();
    $isQuotation = !empty($quotation);

    $title = sprintf(
        // "%s Follow Up Change Status To %s",
        "%s Change Status To %s",
        $isQuotation ? $quotation->quotation_number . " Quotation" : $lead->name . " Lead",
        getStatusName($status)
    );

    $notification = [
        'title' => $title,
        'module_type' => $isQuotation ? 'Quotation Status' : 'Lead Status',
        'module_id' => $isQuotation ? $quotation->id : $lead->id,
        'show_ids' => $ids,
        'event_ids' => [],
        'message' => null,
    ];

    createNotification(...$notification);
}

function createNotification($title, $module_type, $module_id = null, $show_ids = null, $event_ids = null, $message = null)
{
    $user_id = Auth::user()->uuid;

    # Ensure show_ids is an array
    $show_ids = is_array($show_ids) ? array_unique($show_ids) : [];

    # Remove the logged-in user from show_ids
    $filtered_show_ids = array_diff($show_ids, [$user_id]);

    $notification = new Notification();
    $notification->title = $title;
    $notification->module_type = $module_type;
    $notification->user_id = $user_id;
    $notification->module_id = $module_id; # Lead ,Client, Quotation ,SRM (visit site) , Contract , ServiceScheduling ,Invoice
    $notification->show_ids = !empty($show_ids) ? json_encode($show_ids) : null;
    $notification->event_ids = $event_ids ? json_encode($event_ids) : null; # Lead Ids , 
    $notification->message = $message;
    $notification->save();

    # Send notifications to other users
    foreach ($filtered_show_ids as $receiver_id) {
        // try {
        //     event(new SendMessage("You have a new notification!", $receiver_id));
        // } catch (\Exception $e) {
        //     createExceptionError($e, COMMON_HELPER, __FUNCTION__);
        // }
    }
}
