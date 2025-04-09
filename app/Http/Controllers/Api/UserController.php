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
            $list['roles'] = Role::select('id', 'name')->where('slug', '!=', 'super-admin')->get();
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
                $query->where('name', 'ILIKE', "%{$search}%");
            }

            # Sort results
            if ($sortKey = $request->input('sort_key')) {
                $sortOrder = $request->input('sort_order', 'asc');
                $query->orderBy($sortKey, $sortOrder);
            }

            # Pagination
            $perPage = $request->input('per_page', 10);
            $list = $query->with('roles')->paginate($perPage);

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
            $list = $query->with('roles')->paginate($perPage);

            return $this->actionSuccess('User list retrieved successfully.', customizingResponseData($list));
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'status'     => 'required|in:Active,In-Active',
            'email'      => 'required|email|max:255|unique:users,email',
            // 'image'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== 'null' && !preg_match('/^data:image\/(jpg|jpeg|png);base64,/', $value)) {
                    $fail('The ' . $attribute . ' field must be a valid base64 image (jpg, jpeg, png).');
                }
            }],
            'user_name'  => 'required|string|max:255|unique:users,user_name',
            'password'   => 'required|string|min:6',
            'roles'      => 'required',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();

        try {
            $imagePath = null;

            if ($request->filled('image') && $request->image !== 'null') {
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $request->image);
                $imageName = time() . '.jpg';
                $directory = 'UserImages';
                $imagePath = $directory . '/' . $imageName;

                Storage::disk('public')->makeDirectory($directory, 0755, true, true);
                Storage::disk('public')->put($imagePath, base64_decode($imageData));
            }

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'status'    => $request->status,
                'avatar'    => $imagePath ? 'storage/' . $imagePath : null,
                'user_name' => $request->user_name,
                'password'  => Hash::make($request->password),
                // 'created_by' => $this->login_user?->uuid,
            ]);

            $roleIds = Role::whereIn('id', explode(',', $request->roles))->pluck('id')->toArray();
            $user->roles()->sync($roleIds);

            DB::commit();

            return $this->actionSuccess('User created successfully.', $user);
        } catch (\Throwable $e) {
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
            'status' => 'required|in:Active,In-Active',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->id)],
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'user_name' => ['required', 'max:255', Rule::unique('users', 'user_name')->ignore($request->id)],
            'roles' => 'required',
        ];

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
                $imageName = time() . '.jpg'; # Generate unique filename
                $directory = 'UserImages';
                $imagePath = $directory . '/' . $imageName;

                # Find the existing user record
                $user = User::where('id', $request->id)->first();
                if ($user && $user->avatar) {
                    $existingImagePath = $user->avatar;

                    # Check if the existing image exists and delete it
                    if (Storage::disk('public')->exists($existingImagePath)) {
                        Storage::disk('public')->delete($existingImagePath);
                    }
                }

                # Ensure the directory exists
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory, 0755, true);
                }

                # Decode and store the new image
                $decodedImage = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $image));
                Storage::disk('public')->put($imagePath, $decodedImage);
            }

            $user = User::withTrashed()->where('id', $request->id)->first();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->status = $request->status;
            $user->avatar = $imagePath ? 'storage/' . $imagePath : null;
            $user->user_name = $request->user_name ?? null;
            $user->save();

            $roleIds = [];
            $role_ids = explode(',', $request->roles);
            $roleIds = Role::whereIn('id', $role_ids)->pluck('id')->toArray();
            $user->roles()->sync($roleIds);

            DB::commit();
            return $this->actionSuccess('User Updated successfully.', $user);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function updatePassword(Request $request, $user_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return $this->actionFailure($validator->errors()->first());
            }

            $find_user = User::withTrashed()->where('uuid', $user_id)->first();
            if (!$find_user) {
                return $this->actionFailure('User Not found');
            } else {
                $find_user->password = Hash::make($request->confirmPassword);
                $find_user->save();
                return $this->actionSuccess('Password Updated successfully.', $find_user);
            }
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function userStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,uuid',
            'status' => 'required|in:Active,In-Active',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            # Update Employee Role
            $user = User::withTrashed()->where('uuid', $request->user_id)->with('roles')->first();
            if (!$user) return $this->actionFailure('User not Found!');
            $user->status = $request->status;
            $user->save();
            DB::commit();
            return $this->actionSuccess('User Status updated successfully.', $user);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function userRoleUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,uuid',
            'role_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $user = User::withTrashed()->where('uuid', $request->user_id)->first();
            $roleIds = Role::whereIn('id', $request->role_ids)->pluck('id')->toArray();
            $user->roles()->sync($roleIds);
            DB::commit();
            $info = User::where('uuid', $request->user_id)->with('roles')->first();
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
        $validator = Validator::make(['action' => $request->action, 'uuid' => $request->user_id], [
            'action' => 'required|in:delete,restore,force_delete',
            'uuid' => 'required|exists:users,uuid',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $action = $request->action;
            $user_id = $request->user_id;

            $user = User::withTrashed()->where('uuid', $user_id)->first();
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
                    if (trim($request->delete_text) !== "DELETE") {
                        return $this->actionFailure('Your Delete input value is wrong. If you are permanently deleting the file, please type "DELETE" to confirm!');
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
    public function show(string $user_id)
    {
        $validator = Validator::make(['user_id' => $user_id], ['user_id' => 'required|exists:users,uuid']);
        if ($validator->fails()) return $this->actionFailure($validator->errors()->first());

        try {
            $user = User::withTrashed()->where('uuid', $user_id)->with(['roles'])->first();
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

            # Mail::to($user->email)->send(new ForgetPassword($user, $token));
            return $this->actionSuccess('Password reset link has been sent.');
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    # in this code got this error "This password does not use the Bcrypt algorithm.
    public function resetPasswordNew(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        # Get the reset token record
        $resetRecord = PasswordReset::where('email', $request->email)
            ->where('token', $request->token)
            ->first();
        if (!$resetRecord) {
            return response()->json(['error' => 'Invalid token.'], 400);
        }

        # Check if the token has expired
        $user = User::where('email', $request->email)->first();
        if (Carbon::now()->greaterThan($user->expire_at)) {
            return response()->json(['error' => 'This password reset link has expired.'], 400);
        }

        # Update password
        $user->password = Hash::make($request->password);
        $user->expire_at = null;
        $user->save();

        # Optionally, you can clear the token from the password_reset_tokens table
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


            # Mail::to($user->email)->send(new ForgetPassword($user, $token));

            return $this->actionSuccess('Password reset link has been sent.');
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    # in this code got this error "This password does not use the Bcrypt algorithm.
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

        # Check if the token has expired
        $user = User::where('email', $request->email)->first();
        if (Carbon::now()->greaterThan($user->expire_at)) {
            return response()->json(['error' => 'This password reset link has expired.'], 400);
        }

        # Update password
        $user->password = Hash::make($request->password);
        $user->save();

        # Optionally, you can clear the token from the password_reset_tokens table
        PasswordReset::where('token', $request->token)->delete();
        return response()->json([
            'message' => 'Password reset successfully',
            'data' => $user,
            'status' => 200
        ]);
    }
}
