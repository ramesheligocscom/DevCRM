<?php

namespace Modules\FollowUp\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FollowUp\Transformers\FollowUpResource;
use Illuminate\Http\JsonResponse;
use Modules\FollowUp\Services\FollowUpService;
use Modules\FollowUp\Http\Requests\{FollowUpStoreRequest, FollowUpUpdateRequest};
use Symfony\Component\HttpFoundation\Response;


class FollowUpController extends Controller
{
    protected $followUpService;

    public function __construct(FollowUpService $followUpService)
    {
        $this->followUpService = $followUpService;

    }

    public function index(Request $request): JsonResponse
    {
        $followUps = $this->followUpService->getAllFollowUps()
            ->when($request->boolean('with_trashed'), fn($q) => $q->withTrashed())
            ->when($request->search, fn($q) => $q->search($request->search))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => FollowUpResource::collection($followUps),
            'meta' => $this->buildPaginationMeta($followUps),
            'message' => 'Follow ups retrieved successfully',
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function store(FollowUpStoreRequest $request): JsonResponse
    {
        // dd($request->validated());
        $followUp = $this->followUpService->createFollowUp($request->validated());
        return response()->json([
            'data' => new FollowUpResource($followUp),
            'message' => 'Follow up created successfully',
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function show(string $id): JsonResponse
    {
        $followUp = $this->followUpService->getFollowUpById($id);
        return response()->json([
            'data' => new FollowUpResource($followUp),
            'message' => 'Follow up retrieved successfully',
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function update(FollowUpUpdateRequest $request, string $id): JsonResponse
    {
        $followUp = $this->followUpService->updateFollowUp($id, $request->validated());
        
        return response()->json([
            'data' => new FollowUpResource($followUp),
            'message' => 'Follow up updated successfully',
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->followUpService->deleteFollowUp($id);
        return response()->json([
            'message' => 'Follow up deleted successfully',
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
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
