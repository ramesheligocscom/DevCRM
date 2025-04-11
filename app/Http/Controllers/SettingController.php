<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\StateResource;
use App\Models\AdminControlConfig;
use App\Models\State;
use App\Models\Setting;
use App\Models\City;
use App\Models\Page;
use App\Models\PageStatus;
use App\Models\Source;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
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
            if ($request->has('image')) {
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
            if ($sortKey = $request->input('sort_key')) {
                $sortOrder = $request->input('sort_order', 'asc');
                $query->orderBy($sortKey, $sortOrder);
            }

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
        try {
            $info = null;
            return $this->actionSuccess('Status Create successfully',  $info);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function pageStatusUpdate(Request $request)
    {
        try {
            $info = null;
            return $this->actionSuccess('Status Update successfully',  $info);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function pageStatusDelete(Request $request)
    {
        try {
            $info = null;
            return $this->actionSuccess('Status Delete successfully',  $info);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }
}
