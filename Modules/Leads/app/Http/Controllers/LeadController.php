<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Leads\Http\Requests\{LeadStoreRequest, LeadUpdateRequest};
use Modules\Leads\Services\LeadService;
use Modules\Leads\Transformers\LeadResource;
use Symfony\Component\HttpFoundation\Response;

class LeadController extends Controller
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->leadService->getPaginatedLeads(
            $request->integer('per_page', 15),
            $request->boolean('with_trashed'),
            $request->input('status'),
            $request->input('assigned_user')
        );

        return response()->json([
            'data' => LeadResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
            'message' => 'Leads retrieved successfully'
        ]);
    }

    public function store(LeadStoreRequest $request): JsonResponse
    {
        $lead = $this->leadService->createLead(
            array_merge($request->validated(), ['created_by' => auth()->user()->uuid])
        );

        return response()->json([
            'data' => new LeadResource($lead),
            'message' => 'Lead created successfully'
        ], Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $lead = $this->leadService->getLeadById($id);
        return response()->json([
            'data' => new LeadResource($lead),
            'message' => 'Lead retrieved successfully'
        ]);
    }

    public function update(LeadUpdateRequest $request, string $id): JsonResponse
    {
        $lead = $this->leadService->updateLead(
            $id,
            array_merge($request->validated(), ['last_updated_by' => auth()->user()->uuid])
        );

        return response()->json([
            'data' => new LeadResource($lead),
            'message' => 'Lead updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->leadService->deleteLead($id);
        return response()->json([
            'message' => 'Lead deleted successfully'
        ]);
    }

    public function clientsByLead($leadId , Request $request): JsonResponse
    {
        $paginated = $this->leadService->getPaginatedClients(
            $request->integer('per_page', 15),
            $request->boolean('with_trashed'),
            $request->input('status'),
            $leadId
        );

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
            'message' => 'Clients retrieved successfully'
        ]);
    }

}
