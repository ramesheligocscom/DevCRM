<?php

namespace Modules\Contracts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Contracts\Http\Requests\{ContractStoreRequest, ContractUpdateRequest};
use Modules\Contracts\Services\ContractService;
use Modules\Contracts\Transformers\ContractResource;
use Symfony\Component\HttpFoundation\Response;

class ContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->contractService->getPaginatedContracts(
            $request->integer('per_page', 15),
            $request->boolean('with_trashed'),
            $request->input('status'),
            $request->input('client_id')
        );

        return response()->json([
            'data' => ContractResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
            'message' => 'Contracts retrieved successfully'
        ]);
    }

    public function store(ContractStoreRequest $request): JsonResponse
    {
        $contract = $this->contractService->createContract(
            array_merge($request->validated(), ['created_by' => auth()->id()])
        );

        return response()->json([
            'data' => new ContractResource($contract),
            'message' => 'Contract created successfully'
        ], Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $contract = $this->contractService->getContractById($id);
        return response()->json([
            'data' => new ContractResource($contract),
            'message' => 'Contract retrieved successfully'
        ]);
    }

    public function update(ContractUpdateRequest $request, string $id): JsonResponse
    {
        $contract = $this->contractService->updateContract(
            $id,
            array_merge($request->validated(), ['last_updated_by' => auth()->id()])
        );

        return response()->json([
            'data' => new ContractResource($contract),
            'message' => 'Contract updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->contractService->deleteContract($id);
        return response()->json([
            'message' => 'Contract deleted successfully'
        ]);
    }
}
