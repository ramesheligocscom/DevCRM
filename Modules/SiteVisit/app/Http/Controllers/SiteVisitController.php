<?php

namespace Modules\SiteVisit\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SiteVisit\Services\SiteVisitService;
use Illuminate\Http\{JsonResponse, Request};
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

    public function index(Request $request): JsonResponse
    {
        $visits = $this->siteVisitService->getAllVisits()
            ->when($request->boolean('with_trashed'), fn($q) => $q->withTrashed())
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => SiteVisitResource::collection($visits),
            'meta' => $this->buildPaginationMeta($visits),
            'message' => 'Site visits retrieved successfully',
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function store(StoreSiteVisitRequest $request): JsonResponse
    {
        $visit = $this->siteVisitService->createVisit($request->validated());

        return response()->json([
            'message' => __('Site visit created successfully'),
            'data' => new SiteVisitResource($visit),
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function show(string $id): JsonResponse
    {
        $visit = $this->siteVisitService->getVisitById($id);
        return response()->json([
            'data' => new SiteVisitResource($visit),
            'message' => 'Site visit retrieved successfully',
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function update(UpdateSiteVisitRequest $request, string $id): JsonResponse
    {
        $visit = $this->siteVisitService->updateVisit($id, $request->validated());
        return response()->json([
            'data' => new SiteVisitResource($visit),
            'message' => 'Site visit updated successfully',
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->siteVisitService->deleteVisit($id);
            return response()->json([
                'message' => 'Site visit deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Site visit not found',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete site visit',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
