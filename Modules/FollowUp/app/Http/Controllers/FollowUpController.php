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
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        $followUps = $this->followUpService->getAllFollowUps();
        return response()->json([
            'data' => FollowUpResource::collection($followUps),
            'message' => 'Follow ups retrieved successfully'
        ]);
    }

    public function store(FollowUpStoreRequest $request): JsonResponse
    {
        $followUp = $this->followUpService->createFollowUp($request->validated());
        return response()->json([
            'data' => new FollowUpResource($followUp),
            'message' => 'Follow up created successfully'
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $followUp = $this->followUpService->getFollowUpById($id);
        return response()->json([
            'data' => new FollowUpResource($followUp),
            'message' => 'Follow up retrieved successfully'
        ]);
    }

    public function update(FollowUpUpdateRequest $request, string $id): JsonResponse
    {
        $followUp = $this->followUpService->updateFollowUp($id, $request->validated());
        return response()->json([
            'data' => new FollowUpResource($followUp),
            'message' => 'Follow up updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->followUpService->deleteFollowUp($id);
        return response()->json([
            'message' => 'Follow up deleted successfully'
        ]);
    }
}
