<?php

namespace Modules\ProductService\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\ProductService\Models\ProductService;

class ProductServiceService
{
    public function getPaginatedProductServices(
        int $perPage = 15,
        bool $withTrashed = false,
        ?string $search = null
    ): LengthAwarePaginator {
        return ProductService::query()
            ->when($withTrashed, fn($q) => $q->withTrashed())
            ->when($search, fn($q) => $q->search($search))
            ->with(['creator', 'updater'])
            ->latest()
            ->paginate($perPage);
    }

    public function getProductServiceById(string $id): ProductService
    {
        return ProductService::with(['creator', 'updater'])
            ->findOrFail($id);
    }

    public function createProductService(array $data): ProductService
    {
        return ProductService::create($data);
    }

    public function updateProductService(string $id, array $data): ProductService
    {
        $productService = $this->getProductServiceById($id);
        $productService->update($data);
        return $productService->fresh();
    }

    public function deleteProductService(string $id): void
    {
        $productService = $this->getProductServiceById($id);
        $productService->delete();
    }
}
