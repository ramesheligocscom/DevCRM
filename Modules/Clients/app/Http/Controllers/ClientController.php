<?php

namespace Modules\Clients\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Clients\app\Models\Client;
use Modules\Clients\Http\Requests\ClientStoreRequest;
use Modules\Clients\Http\Requests\ClientUpdateRequest;
use Modules\Clients\app\Transformers\ClientResource;


class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        dd("Dfsdf");
        $query = Client::query();

        // Filter non-deleted clients by default
        if ($request->boolean('with_trashed', false)) {
            $query->withTrashed();
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assigned user if provided
        if ($request->has('assigned_user')) {
            $query->where('assigned_user', $request->assigned_user);
        }

        $clients = $query->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ClientResource::collection($clients),
            'meta' => [
                'current_page' => $clients->currentPage(),
                'per_page' => $clients->perPage(),
                'total' => $clients->total(),
            ]
        ]);
    }

    public function store(ClientStoreRequest $request): JsonResponse
    {
        $client = Client::create([
            'id' => Str::uuid(),
            ...$request->validated(),
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Client created successfully',
            'data' => new ClientResource($client)
        ], 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json([
            'data' => new ClientResource($client->load(['parentClient', 'childClients']))
        ]);
    }

    public function update(ClientUpdateRequest $request, Client $client): JsonResponse
    {
        $client->update([
            ...$request->validated(),
            'last_updated_by' => Auth::id(),
            'last_updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Client updated successfully',
            'data' => new ClientResource($client->fresh())
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->update([
            'is_deleted' => true,
            'last_updated_by' => Auth::id(),
            'last_updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Client marked as deleted successfully'
        ]);
    }
}
