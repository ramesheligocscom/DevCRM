<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminControlConfig;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    const CONTROLLER_NAME = "Setting Controller";

    # Setting List and Save Function 
    public function index(Request $request)
    {
        try {
            $settings = Setting::pluck('value', 'key') ?? [];
            return $this->actionSuccess('Setting list get successfully',  $settings);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $is_delete = $request->is_delete;
            $settings = $request->except(['image', 'is_delete']);
            $userId = Auth::user()->uuid;
            foreach ($settings as $key => $value) {
                $existingSetting = Setting::where('key', $key)->first();

                if ($existingSetting) {
                    # Update only value and updated_by
                    $existingSetting->value = $value;
                    $existingSetting->updated_by = $userId;
                    $existingSetting->save();
                } else {
                    # Insert with created_by
                    Setting::create(['key' => $key, 'value' => $value, 'created_by' => $userId,]);
                }
            }


            // ✅ Delete the logo file if is_delete is true
            if ($is_delete) {
                $existLogo = Setting::where('key', 'company_logo')->first();
                if ($existLogo && $existLogo->value) {
                    $filePath = str_replace('storage/', '', $existLogo->value);
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }

                    // Optional: clear the setting value too
                    $existLogo->value = null;
                    $existLogo->updated_by = $userId;
                    $existLogo->save();
                }
            }

            # Handle Image (Base64)
            if ($request->has('image') && $request->image) {
                $image = $request->image;
                $imageName = time() . '.jpg';
                $imagePath = 'uploads/' . $imageName;

                addStoragePermission("app/public/uploads");

                $decodedImage = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $image));
                Storage::disk('public')->put($imagePath, $decodedImage);

                # Same logic for logo setting
                $logoKey = 'company_logo';
                $existingLogo = Setting::where('key', $logoKey)->first();

                if ($existingLogo) {
                    $existingLogo->value = 'storage/' . $imagePath;
                    $existingLogo->updated_by = $userId;
                    $existingLogo->save();
                } else {
                    Setting::create(['key' => $logoKey, 'value' => 'storage/' . $imagePath, 'created_by' => $userId,]);
                }
            }

            DB::commit();
            return $this->actionSuccess('Settings updated successfully!', Setting::pluck('value', 'key'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->actionFailure($e->getMessage());
        }
    }

    # Status list Function
    public function pageList(Request $request)
    {
        try {
            $pages = AdminControlConfig::pluck('status_for')->unique()->values()->toArray();
            return $this->actionSuccess('Page list get successfully',  $pages);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function pageStatusList(Request $request)
    {
        try {
            $type = $request->type ? $request->type : 'All';
            $query = AdminControlConfig::query();

            if ($type != 'All') $query->where('status_for', $type);

            # Sort results
            $query->orderBy('position', 'asc');

            # Pagination
            $perPage = $request->input('per_page', 10);
            $list = $query->paginate($perPage);

            return $this->actionSuccess('Status list get successfully',  $list);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function pageStatusCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_text'         => 'required|string|max:255',
            'status_color'        => 'required|string',
            'position'            => 'required|integer',
            'status'              => 'required|boolean',
            'status_for'          => 'required|array|min:1',
            'status_for.*'        => 'string|distinct',
            'invoice_footer_text' => 'nullable|string',
            'contract_footer_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        // Check uniqueness of status_text for each status_for value
        foreach ($request->status_for as $statusFor) {
            $exists = AdminControlConfig::where('status_text', $request->status_text)->where('status_for', $statusFor)->exists();

            if ($exists) {
                return $this->actionFailure("Status '{$request->status_text}' already exists for '{$statusFor}'.");
            }
        }

        DB::beginTransaction();
        try {
            foreach ($request->status_for as $statusFor) {
                $config = new AdminControlConfig([
                    'status_text'         => $request->status_text,
                    'status_color'        => $request->status_color,
                    'position'            => $request->position,
                    'is_predefined'       => $request->status,
                    'status_for'          => $statusFor,
                    'invoice_footer_text' => $request->invoice_footer_text,
                    'contract_footer_text' => $request->contract_footer_text,
                ]);

                $config->save();
            }

            DB::commit();
            return $this->actionSuccess('Status Create successfully',  $config);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function pageStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'         => 'required',
            'status_text'         => 'required|string|max:255',
            'status_color'        => 'required|string',
            'position'            => 'required|integer',
            'status'              => 'required|boolean',
            'status_for'          => 'required|array|min:1',
            'status_for.*'        => 'string|distinct',
            'invoice_footer_text' => 'nullable|string',
            'contract_footer_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        // Check uniqueness of status_text for each status_for value
        foreach ($request->status_for as $statusFor) {
            $exists = AdminControlConfig::where('id', '!=', $request->id)->where('status_text', $request->status_text)->where('status_for', $statusFor)->exists();

            if ($exists) {
                return $this->actionFailure("Status '{$request->status_text}' already exists for '{$statusFor}'.");
            }
        }

        DB::beginTransaction();
        try {
            foreach ($request->status_for as $statusFor) {
                $config = new AdminControlConfig([
                    'status_text'         => $request->status_text,
                    'status_color'        => $request->status_color,
                    'position'            => $request->position,
                    'is_predefined'       => $request->status,
                    'status_for'          => $statusFor,
                    'invoice_footer_text' => $request->invoice_footer_text,
                    'contract_footer_text' => $request->contract_footer_text,
                ]);

                $config->save();
            }
            DB::commit();
            return $this->actionSuccess('Status Update successfully',  $config);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }
    public function statusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status'         => 'required',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $update = AdminControlConfig::where('id', $request->status_id)->first();
            if (!$update)  return $this->actionFailure('Status info not Found!');
            $update->position = (int) $request->status;
            $update->save();
            DB::commit();
            return $this->actionSuccess('Status Update successfully',  $update);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function changeColorStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_color'         => 'required',
        ]);

        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $update = AdminControlConfig::where('id', $request->status_id)->first();
            if (!$update) return $this->actionFailure('Status info not Found!');
            $update->status_color = $request->status_color;
            $update->save();
            DB::commit();
            return $this->actionSuccess('Change Color Status successfully',  $update);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function pageStatusDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            $info = AdminControlConfig::where('id', $request->status_id)->first();
            if (!$info) return $this->actionFailure('Status info not Found!');
            $info->delete();
            DB::commit();
            return $this->actionSuccess('Status Delete successfully',  $info);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }
}
