<?php

namespace App\Http\Controllers;

use App\Models\TableHeaderManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TableHeaderManageController extends Controller
{
    const CONTROLLER_NAME = "Table Header Mange Controller";

    protected $referer;
    protected $login_user;

    public function __construct()
    {
        $this->referer = request()->header('referer');
        $this->login_user = request()->user() ?? Auth::user() ?? null;
    }

    public function getTableHeaders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        // dd(Auth::user()->uuid, $this->login_user->uuid);
        try {
            $header = TableHeaderManage::where('user_id', $this->login_user->uuid)->where('slug', $request->slug)->first();

            if (!$header) {
                try {
                    DB::beginTransaction();
                    $header = createTableHeaderManage($request->slug);
                    if (!$header) return $this->actionFailure("No matching headers found.");
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->actionFailure($e->getMessage());
                }
            }

            return $this->actionSuccess('Headers retrieved successfully.', $header);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function saveTableHeaders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string',
            'header_list' => 'required|array',
        ]);
        if ($validator->fails()) {
            return $this->actionFailure($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            TableHeaderManage::where('user_id', Auth::user()->uuid)->where('slug', $request->slug)->update(['headers' => json_encode($request->header_list, JSON_UNESCAPED_UNICODE)]);
            DB::commit();
            $header = TableHeaderManage::where('user_id', Auth::user()->uuid)->where('slug', $request->slug)->first();
            return $this->actionSuccess('Table headers saved successfully.', $header);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function tableHeaderSync(Request $request)
    {
        $validator = Validator::make($request->all(), ['slug' => 'required|string']);

        if ($validator->fails()) return response()->json(['error' => $validator->errors()->first()], 422);

        DB::beginTransaction();
        try {
            $update = createTableHeaderManage($request->slug); # login User single header update 
            // $update = syncAllUserTableHeaderManage($request->slug); # all User match slug header update 
            if (!$update) return $this->actionFailure("No matching headers found.");
            DB::commit();
            $header = TableHeaderManage::where('user_id', Auth::user()->uuid)->where('slug', $request->slug)->first();
            return $this->actionSuccess('Headers retrieved successfully.', $header);
        } catch (\Exception $e) {
            DB::rollBack();
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }
}
