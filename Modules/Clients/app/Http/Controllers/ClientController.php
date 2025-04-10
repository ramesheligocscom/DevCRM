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
            // ->when($request->has('assigned_user'), fn($q) => $q->whereAssignedUser($request->assigned_user))
            ->latest()
            ->with(['creator', 'updater','assignedUser'])
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ClientResource::collection($clients),
            'meta' => $this->buildPaginationMeta($clients),
            'status' => Response::HTTP_OK
        ]);
    }

    public function store(ClientStoreRequest $request): JsonResponse
    {
        $client = Client::createWithAttributes([
            ...$request->validated(),
            'created_by' => auth()->user()->uuid
        ]);

        return response()->json([
            'message' => __('Client created successfully'),
            'data' => new ClientResource($client),
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
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
            'last_updated_by' => auth()->user()->uuid
        ]);

        return response()->json([
            'message' => __('Client updated successfully'),
            'data' => new ClientResource($client->fresh()),
            'status' => Response::HTTP_OK
        ]);
    }

    // app/Http/Controllers/ClientController.php
    public function destroy(Client $client): JsonResponse
    {
        try {
            // Check if already soft deleted
            if ($client->trashed()) {
                return response()->json([
                    'message' => __('Client is already deleted')
                ], 409);
            }

            $client->delete(); // This performs the soft delete

            return response()->json([
                'success' => true,
                'message' => __('Client marked as deleted successfully'),
                'status' => Response::HTTP_OK,
                'data' => [
                    'deleted_at' => $client->deleted_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete client'),
                'status' => Response::HTTP_OK,
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // In your ClientController
    public function restore($id): JsonResponse
    {
        try {
            $client = Client::withTrashed()->findOrFail($id);

            if (!$client->trashed()) {
                return response()->json([
                    'message' => __('Client is not deleted')
                ], 409);
            }

            $client->restore();

            return response()->json([
                'success' => true,
                'message' => __('Client restored successfully'),
                'data' => $client
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to restore client'),
                'error' => $e->getMessage()
            ], 500);
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
