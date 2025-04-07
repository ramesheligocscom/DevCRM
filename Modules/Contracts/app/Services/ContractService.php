<?php

namespace Modules\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Contracts\Models\Contract;

class ContractService
{
    public function getPaginatedContracts(
        int $perPage = 15,
        bool $withTrashed = false,
        ?string $status = null,
        ?string $clientId = null
    ): LengthAwarePaginator {
        return Contract::query()
            ->when($withTrashed, fn($q) => $q->withTrashed())
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->with(['client', 'quotation', 'invoice', 'creator', 'updater'])
            ->latest()
            ->paginate($perPage);
    }

    public function getContractById(string $id): Contract
    {
        return Contract::with(['client', 'quotation', 'invoice', 'creator', 'updater'])
            ->findOrFail($id);
    }

    public function createContract(array $data): Contract
    {
        return Contract::create($data);
    }

    public function updateContract(string $id, array $data): Contract
    {
        $contract = $this->getContractById($id);
        $contract->update($data);
        return $contract->fresh();
    }

    public function deleteContract(string $id): void
    {
        $contract = $this->getContractById($id);
        $contract->delete();
    }

    public function calculateTotals(array $items): array
    {
        $subTotal = collect($items)->sum('price');
        $tax = $subTotal * 0.15;
        $total = $subTotal + $tax;

        return [
            'sub_total' => $subTotal,
            'tax' => $tax,
            'total' => $total
        ];
    }
}
