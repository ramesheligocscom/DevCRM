<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

class AuthController extends Controller
{
    const CONTROLLER_NAME = "Auth Controller";

    public function signIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'remember_me' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        try {

            # Retrieve the user by username, email, or phone
            $user = User::where('email', $request->email)
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->actionFailure('Invalid credentials. Please check your email or password.');
            }

            # Log in the user (not required for Sanctum token issuing, but optional)
            $token = $user->createToken('API Token')->plainTextToken;

            $response = [
                'access_token' => $token,
                'permissions' => [], # $user->getPermissionsViaRoles(),
                'user' => $user->makeHidden('roles'),
                'status' => true
            ];

            return $this->actionSuccess("Login Successfully", $response);
        } catch (\Exception $e) {
            # createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user_id = Auth::id();
            $user = User::where('id', $user_id)->first();

            if (!$user) {
                return $this->actionFailure('User not found.');
            }

            if (Hash::check($request->old_password, $user->password)) {
                $user->password = $request->password;
                $user->save();

                return $this->actionSuccess('Successfully changed password.');
            }

            return $this->actionFailure('Old password does not match.');
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function getProfile()
    {
        try {
            $user = Auth::user();
            return $this->actionSuccess('Profile show successfully.', $user);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = User::findOrFail(Auth::user()->id);

            if (empty($user)) {
                return $this->actionFailure('User not found.');
            }

            if (isset($request['image']) && !empty($request['image'])) {
                $user->clearMediaCollection(User::USER_IMAGE);
                $user->addMedia($request['image'])->toMediaCollection(User::USER_IMAGE, config('app.media_disc'));
            }

            $user->update($request->all());

            return $this->actionSuccess('Profile updated successfully.', $user);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function fetchSettings()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->actionFailure('User not authenticated.');
            }

            # Retrieve the settings for the authenticated user
            $settings = null;
            // $settings = Setting::where('user_id', $user->uuid)->pluck('value', 'key')->toArray();

            return response()->json([
                'success' => true,
                'message' => 'General settings retrieved successfully.',
                'settings' => $settings,
            ]);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function updateSettings(Request $request)
    {
        try {

            $user = User::findOrFail(Auth::user()->id);

            if (empty($user)) {
                return $this->actionFailure('User not found.');
            }

            $file = $request->file('logo');

            if ($file) {
                # Get original file name and extension
                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                # Create unique file name
                $uniqueFileName = $originalFileName . '.' . $extension;

                # Store file
                $path = $file->storeAs('uploads', $uniqueFileName, 'public');
                $url = Storage::url($path);

                // # Update logo-related settings
                // Setting::updateOrCreate(
                //     ['key' => 'site_logo', 'user_id' => $user->uuid],
                //     ['value' => $uniqueFileName]
                // );

                // Setting::updateOrCreate(
                //     ['key' => 'logo_url', 'user_id' => $user->uuid],
                //     ['value' => $url]
                // );
            }

            # Update other settings
            $settings = [
                'site_name' => $request->input('name'),
                'site_email' => $request->input('email'),
            ];

            foreach ($settings as $key => $value) {
                // Setting::updateOrCreate(
                //     ['key' => $key, 'user_id' => $user->uuid],
                //     ['value' => $value]
                // );
            }

            # Retrieve all updated settings
            $updatedSettings = [];
            // $updatedSettings = Setting::where('user_id', $user->uuid)->pluck('value', 'key')->toArray();

            # Return response with success message and updated settings
            return response()->json([
                'success' => true,
                'message' => 'General settings updated successfully.',
                'settings' => $updatedSettings,
            ]);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->actionFailure('No active session found.');
            }

            $ip = request()->ip();
            $location = Location::get($ip);

            # Log the logout before revoking token
            UserLoginLog::create([
                'user_id'    => $user->uuid,
                'ip_address' => $ip,
                'user_agent' => request()->header('User-Agent'),
                'event'      => 'logout',
                'success'    => true,
                'logged_at'  => now(),
                'country'    => $location?->countryName,
                'state'      => $location?->regionName,
                'city'       => $location?->cityName,
            ]);

            # Revoke token and logout
            $user->token()->revoke();
            # Auth::logout();

            return $this->actionSuccess('User logged out successfully.');
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    # User Login Log
    public function getUserLoginLogs(Request $request)
    {
        try {

            # Get logged-in user and their roles
            $user =  request()->user() ?? Auth::user() ?? null;
            $roleSlugs = $user->roles()->pluck('slug')->toArray();

            $query = UserLoginLog::query();

            # Apply filtering if the user is NOT a Super Admin
            // if ($user->account_type !== User::SUPER_ADMIN && (!in_array(RolePermissionConst::SLUG_SUPER_ADMIN, $roleSlugs) || !in_array(RolePermissionConst::SLUG_ADMIN, $roleSlugs))) {
            // $query->where('user_id', $user->uuid);
            // }

            # Filter by search query
            if ($search = $request->input('search')) {
                $query->whereHas('user', function ($qur) use ($search) {
                    $qur->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            # Sort results
            if ($sortKey = $request->input('sort_key')) {
                $sortOrder = $request->input('sort_order', 'asc');
                $query->orderBy($sortKey, $sortOrder);
            }

            # Pagination
            $perPage = $request->input('per_page', 10);
            $list = $query->with('user')->orderBy('logged_at', 'desc')->paginate($perPage);

            return $this->actionSuccess('User logs retrieved successfully.', customizingResponseData($list));
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function deleteLoginLog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,restore,force_delete',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $action = $request->action;
            $login_log_id = $request->login_log_id;

            $UserLoginLog = UserLoginLog::findOrFail($login_log_id);
            if ($action  == 'force_delete') {
                if (strtolower(trim($request->delete_text)) !== 'delete') {
                    return $this->actionFailure('Your Delete input value is wrong. If you are permanently deleting the file, please type "delete" to confirm!');
                }
                $UserLoginLog->Delete();
                $message = 'User Login Log permanently deleted successfully.';
            } else {
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

    public function logUnauthenticatedAccess(Request $request)
    {
        try {
            if (!User::where('id', $request->user_id)->exists()) {
                return $this->actionFailure("User is not exists");
            }
            $ip = $request->ip();
            $location = Location::get($ip);

            $log = UserLoginLog::create([
                'user_id'    => $request->user_id,
                'ip_address' => $ip,
                'user_agent' => $request->header('User-Agent'),
                'event'      => 'Unauthenticated',
                'success'    => false,
                'logged_at'  => now(),
                'country'    => $location?->countryName ?? 'Unknown',
                'state'      => $location?->regionName ?? 'Unknown',
                'city'       => $location?->cityName ?? 'Unknown',
            ]);
            return $this->actionSuccess('Unauthenticated access logged successfully', $log);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }
}
