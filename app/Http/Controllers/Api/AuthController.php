<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

            

             // Log in the user (not required for Sanctum token issuing, but optional)
             $token = $user->createToken('API Token')->plainTextToken;


            $response = [
                'access_token' => $token,
                'permissions' => [] ,// $user->getPermissionsViaRoles(),
                'user' => $user->makeHidden('roles'),
                'status' => true
            ];

            return $this->actionSuccess("Login Successfully", $response);
        } catch (\Exception $e) {
            // createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function signInhhh(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'remember_me' => 'nullable|boolean',  // Adding remember me validation
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        try {
            // Retrieve the user by email
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->actionFailure('Invalid email or password.');
            }

            $credentials = ['email' => $user->email, 'password' => $request->password];

            // Check if remember_me is true and attempt login accordingly
            if ($request->remember_me) {
                auth('web')->attempt($credentials, true); // true for "remember me"
            } else {
                auth('web')->attempt($credentials); // Normal login
            }

            // Generate API token
            $token = $user->createToken('API Token')->accessToken;

            // Prepare the response
            $response = [
                'access_token' => $token,
                'permissions' => $user->getPermissionsViaRoles(),
                'user' => $user->makeHidden('roles'),
            ];

            return $this->actionSuccess("Login Successfully", $response);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }


    // public function ResetPassword(Request $request)
    // {
    //     $resetData = PasswordReset::where('token', $request->token)->where('email', $request->email)->first();
    //     if (isset($resetData)) {
    //         return view('User.resetpassword', compact('resetData'));
    //     }

    //     return $this->actionFailure('Something wrong or not found.');
    // }

    // public function userSetpassword(RequestsResetPassword $request)
    // {
    //     $user = User::whereEmail($request->email)->first();
    //     $user->password = $request->password;
    //     $user->save();

    //     PasswordReset::whereEmail($user->email)->delete();

    //     return view('User.success');
    // }

    public function changePassword(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

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
                $user->addMedia($request['image'])->toMediaCollection(
                    User::USER_IMAGE,
                    config('app.media_disc')
                );
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

            // Retrieve the settings for the authenticated user
            $settings = Setting::where('user_id', $user->uuid)
                ->pluck('value', 'key')
                ->toArray();

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
                // Get original file name and extension
                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();

                // Create unique file name
                $uniqueFileName = $originalFileName . '.' . $extension;

                // Store file
                $path = $file->storeAs('uploads', $uniqueFileName, 'public');
                $url = Storage::url($path);

                // Update logo-related settings
                Setting::updateOrCreate(
                    ['key' => 'site_logo', 'user_id' => $user->uuid],
                    ['value' => $uniqueFileName]
                );

                Setting::updateOrCreate(
                    ['key' => 'logo_url', 'user_id' => $user->uuid],
                    ['value' => $url]
                );
            }

            // Update other settings
            $settings = [
                'site_name' => $request->input('name'),
                'site_email' => $request->input('email'),
            ];

            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key, 'user_id' => $user->uuid],
                    ['value' => $value]
                );
            }

            // Retrieve all updated settings
            $updatedSettings = Setting::where('user_id', $user->uuid)->pluck('value', 'key')->toArray();

            // Return response with success message and updated settings
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
            // $location = Location::get($ip);

            # Log the logout before revoking token
            UserLoginLog::create([
                'user_id'    => $user->id,
                'ip_address' => $ip,
                'user_agent' => request()->header('User-Agent'),
                'event'      => 'logout',
                'success'    => true,
                'logged_at'  => now(),
                // 'country'    => $location?->countryName,
                // 'state'      => $location?->regionName,
                // 'city'       => $location?->cityName,
            ]);

            // Revoke token and logout
            $user->token()->revoke();
            // Auth::logout();

            return $this->actionSuccess('User logged out successfully.');
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }


    public function logoutOld()
    {
        try {
            $token = Auth::user()?->token();

            if ($token) {
                $token->revoke();
                return $this->actionSuccess('User logged out successfully.');
            }

            return $this->actionFailure('No active session found.');
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }
}
