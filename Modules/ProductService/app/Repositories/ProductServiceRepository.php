<?php

namespace Modules\ProductService\Repositories;

use Modules\ProductService\Models\ProductService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductServiceRepository
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = ProductService::query();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query->latest()->paginate($perPage);
    }

    public function withTrashed(): self
    {
        $this->query->withTrashed();
        return $this;
    }

    public function search(string $searchTerm): self
    {
        $this->query->where('name', 'like', "%{$searchTerm}%");
        return $this;
    }
}
