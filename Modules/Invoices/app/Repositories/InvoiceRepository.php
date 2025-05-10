<?php

namespace Modules\Invoices\Repositories;

use Modules\Invoices\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class InvoiceRepository
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = Invoice::query();
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
}
