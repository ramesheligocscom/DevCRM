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
        ?string $clientId = null,
        ?string $invoiceId = null,
        ?string $quotationId = null,
        ?string $createdBy = null,
        ?string $lastUpdatedBy = null,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = Contract::query()->when($withTrashed, fn($q) => $q->withTrashed());

        # ✅ Apply custom filtering from the helper
        $query = applyFilteringUser($query, 'created_by');

        return $query->when($status, fn($q) => $q->where('status', $status))
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->when($invoiceId, fn($q) => $q->where('invoice_id', $invoiceId))
            ->when($quotationId, fn($q) => $q->where('quotation_id', $quotationId))
            ->when($createdBy, fn($q) => $q->where('created_by', $createdBy))
            ->when($lastUpdatedBy, fn($q) => $q->where('last_updated_by', $lastUpdatedBy))
            ->when($search, fn($q) => $q->search($search))
            ->with(['creator', 'updater'])
            ->latest()
            ->paginate($perPage);
    }

    public function getContractById(string $id): Contract
    {
        return Contract::with(['creator', 'updater'])
            ->findOrFail($id);
    }

    public function createContract(array $data): Contract
    {
        // Calculate totals before creation
        $totals = $this->calculateTotals($data['items'] ?? []);
        $data = array_merge($data, $totals);
        return Contract::create($data);
    }

    public function updateContract(string $id, array $data): Contract
    {
        $contract = $this->getContractById($id);

        // Calculate totals before update
        $totals = $this->calculateTotals($data['items'] ?? []);
        $data = array_merge($data, $totals);

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
        $subTotal = collect($items)->sum('subtotal');
        $total = collect($items)->sum('total');
        $discount = collect($items)->sum('discount_amount');
        $tax = $subTotal * 0.15;


        return [
            'sub_total' => $subTotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total
        ];
    }
}
