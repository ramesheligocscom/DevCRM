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

    public function index(): JsonResponse
    {
        $followUps = $this->followUpService->getAllFollowUps();
        return response()->json([
            'data' => FollowUpResource::collection($followUps),
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
            'status' => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);
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
}
