<?php

namespace App\Http\Controllers\Api;

use App\Events\SendMessage;
use App\Http\Controllers\Controller;
use App\Mail\ForgetPassword;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Modules\RolePermission\Models\Role;
use Modules\RolePermission\Models\UserRole;

class UserController extends Controller
{
    const CONTROLLER_NAME = "User Controller";

    protected $referer;
    protected $login_user;

    public function __construct()
    {
        $this->referer = request()->header('referer');
        $this->login_user = request()->user() ?? Auth::user() ?? null;
    }

    public function updatePassword(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return $this->actionFailure($validator->errors()->first());
            }

            $find_user = User::find($id);
            if (!$find_user) {
                return $this->actionFailure('User Not found');
            } else {
                $find_user->update([
                    'password' =>  Hash::make($request->confirmPassword),
                ]);
                return $this->actionSuccess('Password Updated successfully.', $find_user);
            }
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function dropdownUserList(Request $request)
    {
        try {
            $list = User::where('email', '!=', 'admin@eligocs.com')->where('status', User::ACTIVE)->select('id', 'name')->get();
            return $this->actionSuccess('Dropdown User retrieved successfully.', $list);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function userOptionList(Request $request)
    {
        try {
            $list = [];
            $list['roles'] = Role::select('id', 'name')->where('name', '!=', 'Super Admin')->get();
            $list['employees'] = User::where('is_employee', 0)->where('email', '!=', 'admin@eligocs.com')->with('meta')->get();
            return $this->actionSuccess('User Options retrieved successfully.', (object) $list);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    # TODO: User List
    public function index(Request $request)
    {

        try {
            $user = request()->user();
            $query = User::query()->withTrashed();
            $query->where('id', '!=', $user->id);

            # Filter by search query
            if ($search = $request->input('search')) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            # Sort results
            if ($sortKey = $request->input('sort_key')) {
                $sortOrder = $request->input('sort_order', 'asc');
                $query->orderBy($sortKey, $sortOrder);
            }

            # Pagination
            $perPage = $request->input('per_page', 10);
            $list = $query->with('roles', 'meta:id,user_id,meta_key,meta_value')->paginate($perPage);

            return $this->actionSuccess('User list retrieved successfully.', customizingResponseData($list));
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function getUserActivityTimelineList(Request $request)
    {
        try {
            $user = request()->user();
            $query = User::query()->withTrashed()->where('is_user', 1);
            $query->where('id', '!=', $user->id);

            # Filter by search query
            if ($search = $request->input('search')) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            # Sort results
            if ($sortKey = $request->input('sort_key')) {
                $sortOrder = $request->input('sort_order', 'asc');
                $query->orderBy($sortKey, $sortOrder);
            }

            # Pagination
            $perPage = $request->input('per_page', 10);
            $list = $query->with('roles', 'meta:id,user_id,meta_key,meta_value')->paginate($perPage);

            return $this->actionSuccess('User list retrieved successfully.', customizingResponseData($list));
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gender' => 'nullable',
            'blood_group' => 'nullable',
            'name' => 'required|string|max:255',
            'account_type' => 'required',
            'phone' => 'required|string|max:15|unique:users,phone',
            'status' => 'required|in:Active,In-Active',
            'email' => 'required|email|max:255|unique:users,email',
            'address' => 'nullable|string|max:500',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $is_login =  $request->is_login == "true" || $request->is_login == "1"  ? true : false;
        if ($is_login) {
            $validator = Validator::make($request->all(), [
                'appointment_date' => 'required',
                'join_date' => 'required|date_format:Y-m-d',
                'user_name' => 'required|max:255|unique:users,user_name',
                'password' => 'required|string|min:6',
                'roles' => 'required',
            ]);
        }

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {

            # File Upload
            // $imagePath = null;
            // if ($request->hasFile('profile')) {
            //     $imagePath = $request->file('profile')->store('user_images', 'public');
            // }

            $imagePath = null;

            if ($request->image != 'null') {
                $image = $request->image;
                $imageName = time() . '.jpg'; // Generate unique filename
                $directory = 'employeeUserUploads';
                $imagePath = $directory . '/' . $imageName;

                // Ensure the directory exists
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory, 0755, true);
                }

                // Decode and store the image
                $decodedImage = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $image));
                Storage::disk('public')->put($imagePath, $decodedImage);
            }

            $is_employee =  $request->is_employee == "true" || $request->is_employee == "1"  ? true : false;
            $is_user =  $request->is_user == "true" || $request->is_user == "1"  ? true : false;

            $user = new User();
            $user->employee_code = $request->emp_code ?? null;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->account_type = $request->account_type;
            $user->phone_code = "+91" ?? $request->phone_code;
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->image = $imagePath ? 'storage/' . $imagePath : null;
            $user->is_employee = $is_employee;
            $user->is_user = $is_user;
            $user->is_login = $is_login;
            $user->user_name = $request->user_name ?? null;
            $user->password = Hash::make($is_login ? $request->password : '');
            $user->created_by = Auth::id() ?? null;
            $user->save();

            $roleIds = [];
            if ($is_login) {
                $role_ids = explode(',', $request->roles);
                $roleIds = Role::whereIn('id', $role_ids)->pluck('id')->toArray();
                $user->roles()->sync($roleIds);
            } else {
                UserRole::where('user_id', $user->id)->delete();
            }

            // $user_metas = [
            //     ['meta_key' => 'gender', 'meta_value' => $request->gender],
            //     ['meta_key' => 'user_image', 'meta_value' => $imagePath],
            //     ['meta_key' => 'address', 'meta_value' => $request->address ?? ''],
            //     ['meta_key' => 'blood_group', 'meta_value' => $request->blood_group ?? ''],
            //     ['meta_key' => 'join_date', 'meta_value' => $is_login ? $request->join_date : ''],
            //     ['meta_key' => 'appointment_date', 'meta_value' => $is_login ? $request->appointment_date : ''],
            //     ['meta_key' => 'user_pass', 'meta_value' => $is_login ? $request->password : ''],
            // ];

            // foreach ($user_metas as $key => $meta) {
            //     UserMeta::updateOrCreate(['user_id' => $user->id, 'meta_key' => $meta['meta_key']], [
            //         'user_id' => $user->id,
            //         'meta_key' => $meta['meta_key'],
            //         'meta_value' => $meta['meta_value'],
            //     ]);
            // }

            DB::commit();
            return $this->actionSuccess('User created successfully.', $user);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'account_type' => 'required',
            'phone' => ['required', 'string', 'max:15', Rule::unique('users', 'phone')->ignore($request->id)],
            'join_date' => 'required|date_format:Y-m-d',
            'status' => 'required|in:Active,In-Active',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->id)],
            'address' => 'nullable|string|max:500',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        $is_login =  $request->is_login == "true" || $request->is_login == "1"  ? true : false;

        if ($is_login) {
            $rules = array_merge($rules, [
                'appointment_date' => 'required|date_format:Y-m-d',
                'user_name' => ['required', 'max:255', Rule::unique('users', 'user_name')->ignore($request->id)],
                'password' => 'required|string|min:6',
                'roles' => 'required',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            # File Upload
            $imagePath = null;

            if ($request->image != 'null') {
                $image = $request->image;
                $imageName = time() . '.jpg'; // Generate unique filename
                $directory = 'employeeUserUploads';
                $imagePath = $directory . '/' . $imageName;

                // Find the existing user record
                $user = User::where('phone', $request->phone)->first();
                if ($user && $user->image) {
                    $existingImagePath = $user->image;

                    // Check if the existing image exists and delete it
                    if (Storage::disk('public')->exists($existingImagePath)) {
                        Storage::disk('public')->delete($existingImagePath);
                    }
                }

                // Ensure the directory exists
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory, 0755, true);
                }

                // Decode and store the new image
                $decodedImage = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $image));
                Storage::disk('public')->put($imagePath, $decodedImage);
            }



            $is_employee =  $request->is_employee == "true" || $request->is_employee == "1"  ? true : false;
            $is_user =  $request->is_user == "true" || $request->is_user == "1"  ? true : false;

            $user = User::withTrashed()->where('id', $request->id)->first();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->account_type = $request->account_type;
            $user->phone = $request->phone;
            $user->status = $request->status;
            $user->phone_code = "+91" ?? $request->phone_code;
            $user->image = $imagePath ? 'storage/' . $imagePath : null;
            $user->is_employee = $is_employee;
            $user->is_user = $is_user;
            $user->is_login = $is_login;
            $user->user_name = $request->user_name ?? null;
            $user->password = Hash::make($is_login ? $request->password : '123400');
            $user_metas = [];

            $user->save();

            $roleIds = [];
            if ($is_login) {
                $role_ids = explode(',', $request->roles);
                $roleIds = Role::whereIn('id', $role_ids)->pluck('id')->toArray();
                $user->roles()->sync($roleIds);
            } else {
                UserRole::where('user_id', $user->id)->delete();
            }

            // $user_metas = [
            //     ['meta_key' => 'gender', 'meta_value' => $request->gender],
            //     ['meta_key' => 'user_image', 'meta_value' => $imagePath],
            //     ['meta_key' => 'address', 'meta_value' => $request->address ?? ''],
            //     ['meta_key' => 'blood_group', 'meta_value' => $request->blood_group ?? ''],
            //     ['meta_key' => 'join_date', 'meta_value' => $is_login ? $request->join_date : ''],
            //     ['meta_key' => 'appointment_date', 'meta_value' => $is_login ? $request->appointment_date : ''],
            //     ['meta_key' => 'user_pass', 'meta_value' => $is_login ? $request->password : '123400'],
            // ];

            // foreach ($user_metas as $key => $meta) {
            //     UserMeta::updateOrCreate(['user_id' => $user->id, 'meta_key' => $meta['meta_key']], [
            //         'user_id' => $user->id,
            //         'meta_key' => $meta['meta_key'],
            //         'meta_value' => $meta['meta_value'],
            //     ]);
            // }

            DB::commit();
            return $this->actionSuccess('User created successfully.', $user);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function userStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:Active,In-Active',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $user = User::withTrashed()->findOrFail($request->user_id);
            # Update Employee Role
            $user->update(['status' => $request->status]);
            DB::commit();
            $info = User::where('id', $request->user_id)->with('roles', 'meta:id,user_id,meta_key,meta_value')->first();
            return $this->actionSuccess('User Status updated successfully.', $info);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function userRoleUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $user = User::withTrashed()->findOrFail($request->user_id);
            $roleIds = Role::whereIn('id', $request->role_ids)->pluck('id')->toArray();

            $user->roles()->sync($roleIds);
            DB::commit();
            $info = User::where('id', $request->user_id)->with('roles', 'meta:id,user_id,meta_key,meta_value')->first();
            $info->role_ids = $roleIds;
            return $this->actionSuccess('User Role updated successfully.', $info);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    # Delete, Restore, Force Delete
    public function destroy(Request $request)
    {
        $validator = Validator::make(['action' => $request->action, 'id' => $request->user_id], [
            'action' => 'required|in:delete,restore,force_delete',
            'id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $action = $request->action;
            $user_id = $request->user_id;

            $user = User::withTrashed()->findOrFail($user_id);
            switch ($action) {
                case 'delete':
                    $user->status = 'In-Active';
                    $user->save();
                    $user->delete();
                    $message = 'User deleted successfully.';
                    break;
                case 'restore':
                    $user->status = 'Active';
                    $user->save();
                    $user->restore();
                    $message = 'User restored successfully.';
                    break;
                case 'force_delete':
                    if (strtolower(trim($request->delete_text)) !== 'delete') {
                        return $this->actionFailure('Your Delete input value is wrong. If you are permanently deleting the file, please type "delete" to confirm!');
                    }
                    $user->Delete();
                    $message = 'User permanently deleted successfully.';
                    break;
                default:
                    return $this->actionFailure('Invalid action.');
            }

            DB::commit();
            return $this->actionSuccess($message);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $validator = Validator::make(['id' => $id], ['id' => 'required|exists:users,id']);
        if ($validator->fails()) return $this->actionFailure($validator->errors()->first());

        try {
            $user = User::withTrashed()->with(['meta', 'roles'])->findOrFail($id);
            return $this->actionSuccess('User retrieved successfully.', $user);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function forgotPasswordNew(Request $request)
    {
        try {
            $user = User::whereEmail($request->email)->first();

            if (!$user) {
                return $this->actionFailure('User email not found.');
            }

            $token = Str::random(40);
            $datetime = Carbon::now()->format('Y-m-d H:i:s');
            $expiresAt = Carbon::now()->addMinutes(2);
            PasswordReset::updateOrInsert(
                ['email' => $request->email],
                ['email' => $request->email, 'token' => $token, 'created_at' => $datetime]
            );

            User::withTrashed()->where('email', $request->email)->update([
                'expire_at' => $expiresAt,
            ]);

            // Mail::to($user->email)->send(new ForgetPassword($user, $token));
            return $this->actionSuccess('Password reset link has been sent.');
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    // in this code got this error "This password does not use the Bcrypt algorithm.
    public function resetPasswordNew(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Get the reset token record
        $resetRecord = PasswordReset::where('email', $request->email)
            ->where('token', $request->token)
            ->first();
        if (!$resetRecord) {
            return response()->json(['error' => 'Invalid token.'], 400);
        }

        // Check if the token has expired
        $user = User::where('email', $request->email)->first();
        if (Carbon::now()->greaterThan($user->expire_at)) {
            return response()->json(['error' => 'This password reset link has expired.'], 400);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->expire_at = null;
        $user->save();

        // Optionally, you can clear the token from the password_reset_tokens table
        PasswordReset::where('token', $request->token)->delete();
        return response()->json([
            'message' => 'Password reset successfully',
            'data' => $user,
            'status' => 200
        ]);
    }

    public function forgotPassword(Request $request)
    {
        try {
            $user = User::whereEmail($request->email)->first();

            if (!$user) {
                return $this->actionFailure('User email not found.');
            }

            $token = Str::random(40);
            $datetime = Carbon::now()->format('Y-m-d H:i:s');
            $expiresAt = Carbon::now()->addMinutes(2);

            PasswordReset::updateOrInsert(
                ['email' => $request->email],
                ['email' => $request->email, 'token' => $token, 'created_at' => $datetime]
            );

            User::where('email', $request->email)->update([
                'expire_at' => $expiresAt,
            ]);


            // Mail::to($user->email)->send(new ForgetPassword($user, $token));

            return $this->actionSuccess('Password reset link has been sent.');
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    // in this code got this error "This password does not use the Bcrypt algorithm.
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $resetRecord = PasswordReset::where('email', $request->email)
            ->where('token', $request->token)
            ->first();
        if (!$resetRecord) {
            return response()->json(['error' => 'Invalid token.'], 400);
        }

        // Check if the token has expired
        $user = User::where('email', $request->email)->first();
        if (Carbon::now()->greaterThan($user->expire_at)) {
            return response()->json(['error' => 'This password reset link has expired.'], 400);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Optionally, you can clear the token from the password_reset_tokens table
        PasswordReset::where('token', $request->token)->delete();
        return response()->json([
            'message' => 'Password reset successfully',
            'data' => $user,
            'status' => 200
        ]);
    }
}
