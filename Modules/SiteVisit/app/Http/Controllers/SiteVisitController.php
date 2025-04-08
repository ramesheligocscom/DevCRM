<?php

namespace Modules\SiteVisit\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SiteVisit\Services\SiteVisitService;
use Illuminate\Http\JsonResponse;
use Modules\SiteVisit\Http\Requests\{StoreSiteVisitRequest, UpdateSiteVisitRequest};
use Modules\SiteVisit\Transformers\SiteVisitResource;

class SiteVisitController extends Controller
{
    protected $siteVisitService;

    public function __construct(SiteVisitService $siteVisitService)
    {
        $this->siteVisitService = $siteVisitService;
    }

    public function index(): JsonResponse
    {
        $visits = $this->siteVisitService->getAllVisits();
        return response()->json([
            'data' => SiteVisitResource::collection($visits),
            'message' => 'Site visits retrieved successfully'
        ]);
    }

    public function store(StoreSiteVisitRequest $request): JsonResponse
    {
        dd($request->validated());
        $visit = $this->siteVisitService->createVisit($request->validated());
        return response()->json([
            'data' => new SiteVisitResource($visit),
            'message' => 'Site visit created successfully'
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $visit = $this->siteVisitService->getVisitById($id);
        return response()->json([
            'data' => new SiteVisitResource($visit),
            'message' => 'Site visit retrieved successfully'
        ]);
    }

    public function update(UpdateSiteVisitRequest $request, string $id): JsonResponse
    {
        dd($request->validated());
        $visit = $this->siteVisitService->updateVisit($id, $request->validated());
        return response()->json([
            'data' => new SiteVisitResource($visit),
            'message' => 'Site visit updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->siteVisitService->deleteVisit($id);
        return response()->json([
            'message' => 'Site visit deleted successfully'
        ]);
    }
}
