<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\StateResource;
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

class SettingController extends Controller
{
    const CONTROLLER_NAME = "Setting Controller";

    public function index()
    {
        $settings = Setting::where('user_id', 1)->pluck('value', 'key');

        return $this->actionSuccess('', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        try {
            $settings = $request->except(['image']); 

            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            # Handle Image (Base64)
            if ($request->has('image')) {
                $image = $request->image;
                $imageName = time() . '.jpg';  
                $imagePath = 'uploads/' . $imageName;

                # Decode and store the image
                $decodedImage = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $image));
                Storage::disk('public')->put($imagePath, $decodedImage);

                # Save Image Path in DB
                Setting::updateOrCreate(
                    ['key' => 'company_logo'],
                    ['value' => 'storage/' . $imagePath]
                );
            }

            return response()->json(['message' => 'Settings updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function fetchQuotationPerfix()
    {
        try {
            $fetch_quotation_perfix = Setting::where('key', 'Quotation_Prefix')->first();
            if (!$fetch_quotation_perfix) {
                return $this->actionFailure('Quotation Perfix not found');
            }
            return $fetch_quotation_perfix;
        } catch (\Exception $e) {
            return $this->actionFailure($e->getMessage());
        }
    }

    public function fetchInvoicePerfix()
    {
        try {
            $fetch_quotation_perfix = Setting::where('key', 'invoicePrefix')->first();
            if (!$fetch_quotation_perfix) {
                return $this->actionFailure('Invoice Perfix not found');
            }
            return $fetch_quotation_perfix;
        } catch (\Exception $e) {
            return $this->actionFailure($e->getMessage());
        }
    }


    public function saveTermCondition(Request $request)
    {

        try {
            $settings = $request->all();

            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key], # Condition to check existing record
                    ['value' => $value] # Only update the value field
                );
            }

            return response()->json([
                'status' => 200,
                'message' => 'Terms and Conditions have been updated successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Settings update failed: ' . $e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'Failed to update settings. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function states()
    {
        $states = State::get();
        $states = StateResource::collection($states);

        return $this->actionSuccess('State listing successfully.', $states);
    }

    public function cities($stateId)
    {
        $cities = City::where('state_id', $stateId)->get();
        $cities = CityResource::collection($cities);

        return $this->actionSuccess('City listing successfully.', $cities);
    }

    public function storeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|max:255',
            'pages' => 'required|array',
            # 'page.*' => 'exists:pages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        # Create the status
        $status = Status::create([
            'name' => $request->status,
        ]);

        # Create page_status records
        foreach ($request->pages as $page) {
            $page_id = Page::where('name', $page)->first();
            PageStatus::create([
                'page_id' => $page_id->id,
                'status_id' => $status->id,
            ]);
        }

        return response()->json([
            'message' => 'Status and page status created successfully.',
            'status' => $status,
            'response' => 'success'
        ]);

        return $this->actionSuccess('Status Added successfully.');
    }

    public function pageList()
    {
        $page = Page::pluck('name');
        return response()->json(['page' => $page], 201);
    }

    public function pageStatusList(Request $request)
    {
        # Fetch all statuses with their related pages
        $statuses = Status::query()
            ->filter($request)
            ->with('pages')
            ->get();


        # Transform the data into the desired structure
        $result = $statuses->map(function ($status) {
            return [
                'id' => $status->id,
                'status' => $status->name,
                'pages' => $status->pages->map(fn($page) => $page->name)->unique()->values(),
            ];
        });

        # Return the result as JSON
        return response()->json($result);
    }

    public function pageStatus($page_name)
    {
        if ($page_name == 'Lead') {
            if (!Status::where('name', 'Quotation Draft')->exists()) {
                Status::whereIn('name', ['Follow-up', 'In Progress', 'Converted', 'Ready For SRM'])->delete();
                $page_id = Page::where('name', $page_name)->pluck('id')->first();

                $statuses = [
                    ['name' => "No Action", 'slug' => 'no_action'],
                    ['name' => "Follow up", 'slug' => 'follow_up'],
                    ['name' => "Interested", 'slug' => 'interested'],
                    ['name' => "Not Interested", 'slug' => 'not_interested'],
                    ['name' => "Ready For SRM", 'slug' => 'ready_for_srm'],
                    ['name' => "Ready For Quotation", 'slug' => 'ready_for_quotation'],
                    ['name' => "Quotation Created", 'slug' => 'quotation_created'],
                    ['name' => "Quotation Draft", 'slug' => 'quotation_draft'],
                    ['name' => "Quotation in progress 25 %", 'slug' => 'quotation_in_progress_25'],
                    ['name' => "Quotation in progress 50 %", 'slug' => 'quotation_in_progress_50'],
                    ['name' => "Quotation in progress 75 %", 'slug' => 'quotation_in_progress_75'],
                    ['name' => "Quotation Accepted", 'slug' => 'quotation_accepted'],
                    ['name' => "Quotation Cancelled", 'slug' => 'quotation_cancelled'],
                ];

                foreach ($statuses as $status) {
                    $info = Status::updateOrCreate(['slug' => $status['slug']], ['name' => $status['name']]);
                    PageStatus::updateOrCreate(['page_id' => $page_id, 'status_id' => $info->id]);
                }
            }
        }

        try {
            # Get page ID
            $page_id = Page::where('name', $page_name)->pluck('id')->first();

            if (!$page_id) {
                return $this->actionFailure('Page not found');
            }
            # Get associated status IDs from page_statuses table
            $status_ids = PageStatus::where('page_id', $page_id)->pluck('status_id')->toArray();

            # Get status names using the retrieved status IDs
            $status_list = Status::whereIn('id', $status_ids)->pluck('name')->toArray();

            return $this->actionSuccess('Page statuses retrieved successfully', $status_list);
        } catch (\Exception $e) {
            createExceptionError($e, self::CONTROLLER_NAME, __FUNCTION__);
            return $this->actionFailure($e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        # Validate the request data
        $validated = $request->validate([
            'status' => 'required|string',
            'pages' => 'required|array',
            'pages.*' => 'string', # Ensure that pages are strings
        ]);

        # Find the Status record
        $status = Status::findOrFail($id);

        # Update the status name
        $status->name = $validated['status'];
        $status->save(); # Save the updated status name

        # Detach all existing pages related to the status before adding new ones
        $status->pages()->detach(); # Detach previous pages

        # Attach the new pages
        foreach ($validated['pages'] as $pageName) {
            # Find the page by its name or create it
            $page = Page::firstOrCreate(['name' => $pageName]);

            # Attach the page to the status
            $status->pages()->attach($page->id);
        }

        # Return a success response
        return response()->json(['message' => 'Status updated successfully!', 'response' => 'success']);
    }

    public function deleteStatus($id)
    {
        $get_status = Status::with('pages')->find($id);

        if (!$get_status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found.',
            ], 404);
        }

        # Delete related data in the `page_statuses` table
        PageStatus::where('status_id', $id)->delete();
        $get_status->delete();

        return response()->json([
            'success' => true,
            'message' => 'Status and related page statuses deleted successfully.',
        ]);
    }

    public function showUintLists(Request $request)
    {
        $leads = Unit::query()->filter($request)->get();

        # paginate($request->per_page ?? 10);

        return $leads;
    }

    public function showUint(Request $request)
    {
        try {
            # Validation rules
            $rules = [
                'code'           => 'required|string|unique:units,code',
                'name'           => 'required|string|max:255',
                'status'         => 'required|string|max:15',
            ];

            # Validate the request
            $validated = $request->validate($rules);
            # Prepare data for storing
            $data = [
                'code'          => $validated['code'],
                'name'          => $validated['name'],
                'status'        => $validated['status'],
            ];

            # Create a new Unit
            $Unit = Unit::create($data);

            return response()->json([
                'status' => 201,
                'message' => 'Unit created successfully!',
                'data'    => $Unit
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save Unit',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateUint(Request $request, $id)
    {
        try {
            # Validation rules
            $rules = [
                'code'           => 'required|string',
                'name'           => 'required|string|max:255',
                'status'         => 'required|string|max:15',
            ];

            # Validate the request
            $validated = $request->validate($rules);


            $unit = Unit::find($id);

            if (!$unit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit not found.',
                ], 404);
            }

            $unit->update($validated);

            return response()->json([
                'status' => 201,
                'message' => 'Unit Updated successfully!',
                'data'    => $unit
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save Unit',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteUint($id)
    {
        $get_unit = Unit::find($id);

        if (!$get_unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unit not found.',
            ], 404);
        }

        $get_unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit deleted successfully.',
        ]);
    }


    # Curd for Source
    public function storeSource(Request $request)
    {
        try {
            # Validation rules
            $rules = [
                'name'         => 'required|string|max:255',
            ];

            # Validate the request
            $validated = $request->validate($rules);

            # Check if a Source with the same name exists
            $existingLead = Source::where('name', $validated['name'])->first();
            if ($existingLead) {
                return response()->json([
                    "message" => 'This name of Source Exist !!'
                ]);
            } else {
                # Create a new lead
                $Source = Source::create($validated);

                return response()->json([
                    'success' => true,
                    'message' => 'Source created successfully!',
                    'data'    => $Source
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save Source',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
