<?php

namespace Modules\Contracts\Repositories;

use Modules\Contracts\Models\Contract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ContractRepository
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = Contract::query();
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

    public function filterByStatus(string $status): self
    {
        $this->query->where('status', $status);
        return $this;
    }

    public function filterByClient(string $clientId): self
    {
        $this->query->where('client_id', $clientId);
        return $this;
    }

    public function filterByDateRange(string $startDate, string $endDate): self
    {
        $this->query->whereBetween('start_date', [$startDate, $endDate]);
        return $this;
    }
}
