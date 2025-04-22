<?php

namespace Modules\ProductService\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Request};
use Modules\ProductService\Http\Requests\{ProductServiceStoreRequest, ProductServiceUpdateRequest};
use Modules\ProductService\Services\ProductServiceService;
use Modules\ProductService\Transformers\ProductServiceResource;
use Symfony\Component\HttpFoundation\Response;

class ProductServiceController extends Controller
{
    protected $productServiceService;

    public function __construct(ProductServiceService $productServiceService)
    {
        $this->productServiceService = $productServiceService;
    }

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->productServiceService->getPaginatedProductServices(
            $request->integer('per_page', 15),
            $request->boolean('with_trashed'),
            $request->input('search')
        );

        return response()->json([
            'data' => ProductServiceResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'last_page' => $paginated->lastPage(),
            ],
            'message' => 'Products/Services retrieved successfully'
        ]);
    }

    public function store(ProductServiceStoreRequest $request): JsonResponse
    {
        $productService = $this->productServiceService->createProductService(
            array_merge($request->validated(), ['created_by' => auth()->user()->uuid])
        );

        return response()->json([
            'data' => new ProductServiceResource($productService),
            'message' => 'Product/Service created successfully'
        ], Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $productService = $this->productServiceService->getProductServiceById($id);
        return response()->json([
            'data' => new ProductServiceResource($productService),
            'message' => 'Product/Service retrieved successfully'
        ]);
    }

    public function update(ProductServiceUpdateRequest $request, string $id): JsonResponse
    {
        $productService = $this->productServiceService->updateProductService(
            $id,
            array_merge($request->validated(), ['last_updated_by' => auth()->user()->uuid])
        );

        return response()->json([
            'data' => new ProductServiceResource($productService),
            'message' => 'Product/Service updated successfully'
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->productServiceService->deleteProductService($id);
        return response()->json([
            'message' => 'Product/Service deleted successfully'
        ]);
    }
}
