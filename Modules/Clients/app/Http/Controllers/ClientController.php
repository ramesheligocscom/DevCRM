<?php

namespace Modules\Clients\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\Clients\Models\Client;
use Modules\Clients\Http\Requests\{ClientStoreRequest, ClientUpdateRequest};
use Modules\Clients\Transformers\ClientResource;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $clients = Client::query()
            ->when($request->boolean('with_trashed'), fn($q) => $q->withTrashed())
            ->when($request->has('status'), fn($q) => $q->filterByStatus($request->status))
            ->when($request->has('assigned_user'), fn($q) => $q->whereAssignedUser($request->assigned_user))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ClientResource::collection($clients),
            'meta' => $this->buildPaginationMeta($clients)
        ]);
    }

    public function store(ClientStoreRequest $request): JsonResponse
    {
        $client = Client::createWithAttributes([
            ...$request->validated(),
            'created_by' => auth()->id()
        ]);

        return response()->json([
            'message' => __('Client created successfully'),
            'data' => new ClientResource($client)
        ], Response::HTTP_CREATED);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json([
            'data' => new ClientResource($client->loadRelations())
        ]);
    }

    public function update(ClientUpdateRequest $request, Client $client): JsonResponse
    {
        $client->updateWithAttributes([
            ...$request->validated(),
            'last_updated_by' => auth()->id()
        ]);

        return response()->json([
            'message' => __('Client updated successfully'),
            'data' => new ClientResource($client->fresh())
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->softDelete();

        return response()->json([
            'message' => __('Client marked as deleted successfully')
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
