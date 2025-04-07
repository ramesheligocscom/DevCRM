<?php

namespace Modules\Quotations\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Quotations\Models\Quotation;

class QuotationService
{
    public function getPaginatedQuotations(
        int $perPage = 15,
        bool $withTrashed = false,
        ?string $status = null,
        ?string $clientId = null
    ): LengthAwarePaginator {
        return Quotation::query()
            ->when($withTrashed, fn($q) => $q->withTrashed())
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->with(['lead', 'client', 'contract', 'creator', 'updater'])
            ->latest()
            ->paginate($perPage);
    }

    public function getQuotationById(string $id): Quotation
    {
        return Quotation::with(['lead', 'client', 'contract', 'creator', 'updater'])
            ->findOrFail($id);
    }

    public function createQuotation(array $data): Quotation
    {
        return Quotation::create($data);
    }

    public function updateQuotation(string $id, array $data): Quotation
    {
        $quotation = $this->getQuotationById($id);
        $quotation->update($data);
        return $quotation->fresh();
    }

    public function deleteQuotation(string $id): void
    {
        $quotation = $this->getQuotationById($id);
        $quotation->delete();
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
