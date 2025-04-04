<?php

namespace Modules\Clients\Repositories;

use Modules\Clients\app\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ClientRepository
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = Client::query();
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

    public function filterByAssignedUser(string $userId): self
    {
        $this->query->where('assigned_user', $userId);
        return $this;
    }
}
