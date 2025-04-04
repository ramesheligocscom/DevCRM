<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Leads\Models\Leads;
use Modules\Leads\Http\Requests\{LeadStoreRequest, LeadUpdateRequest};
use Modules\Leads\Transformers\LeadResource;
use Symfony\Component\HttpFoundation\Response;

class LeadsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $leads = Leads::query()
            ->when($request->boolean('with_trashed'), fn($q) => $q->withTrashed())
            ->when($request->has('status'), fn($q) => $q->filterByStatus($request->status))
            ->when($request->has('assigned_user'), fn($q) => $q->whereAssignedUser($request->assigned_user))
            ->when($request->has('source'), fn($q) => $q->where('source', $request->source))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => LeadResource::collection($leads),
            'meta' => $this->buildPaginationMeta($leads)
        ]);
    }

    public function store(LeadStoreRequest $request): JsonResponse
    {
        $lead = Leads::createWithAttributes([
            ...$request->validated(),
            'created_by' => auth()->id()
        ]);

        return response()->json([
            'message' => __('Lead created successfully'),
            'data' => new LeadResource($lead)
        ], Response::HTTP_CREATED);
    }

    public function show(Lead $lead): JsonResponse
    {
        return response()->json([
            'data' => new LeadResource($lead->loadRelations())
        ]);
    }

    public function update(LeadUpdateRequest $request, Lead $lead): JsonResponse
    {
        $lead->updateWithAttributes([
            ...$request->validated(),
            'last_updated_by' => auth()->id()
        ]);

        return response()->json([
            'message' => __('Lead updated successfully'),
            'data' => new LeadResource($lead->fresh())
        ]);
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $lead->softDelete();

        return response()->json([
            'message' => __('Lead marked as deleted successfully')
        ]);
    }

    protected function buildPaginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
