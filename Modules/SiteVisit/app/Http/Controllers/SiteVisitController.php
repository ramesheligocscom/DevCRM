<?php

namespace Modules\SiteVisit\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SiteVisit\Services\SiteVisitService;
use Illuminate\Http\JsonResponse;
use Modules\SiteVisit\Http\Requests\{StoreSiteVisitRequest, UpdateSiteVisitRequest};
use Modules\SiteVisit\Transformers\SiteVisitResource;
use Symfony\Component\HttpFoundation\Response;
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
        // dd($visits);
        return response()->json([
            'data' => SiteVisitResource::collection($visits),
            'message' => 'Site visits retrieved successfully'
        ]);
    }

    public function store(StoreSiteVisitRequest $request): JsonResponse
    {
        $visit = $this->siteVisitService->createVisit($request->validated());

        return response()->json([
            'message' => __('Site visit created successfully'),
            'data' => new SiteVisitResource($visit),
        ], Response::HTTP_CREATED);
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
        $visit = $this->siteVisitService->updateVisit($id, $request->validated());
        return response()->json([
            'data' => new SiteVisitResource($visit),
            'message' => 'Site visit updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->siteVisitService->deleteVisit($id);
            return response()->json([
                'message' => 'Site visit deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Site visit not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete site visit',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
