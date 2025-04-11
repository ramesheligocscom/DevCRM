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
        ?string $clientId = null,
        ?string $leadId = null,
        ?string $contractId = null,
        ?string $createdBy = null,
        ?string $lastUpdatedBy = null,
        ?string $search = null,
    ): LengthAwarePaginator {

        $query = Quotation::query()->when($withTrashed, fn($q) => $q->withTrashed());

        # ✅ Apply custom filtering from the helper
        $query = applyFilteringUser($query, 'created_by');

        return $query->when($status, fn($q) => $q->where('status', $status))
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->when($leadId, fn($q) => $q->where('lead_id', $leadId))
            ->when($contractId, fn($q) => $q->where('contract_id', $contractId))
            ->when($createdBy, fn($q) => $q->where('created_by', $createdBy))
            ->when($lastUpdatedBy, fn($q) => $q->where('last_updated_by', $lastUpdatedBy))
            ->when($search, fn($q) => $q->search($search))
            ->with(['creator', 'updater'])
            ->latest()
            ->paginate($perPage);
    }


    public function getQuotationById(string $id): Quotation
    {
        return Quotation::with(['creator', 'updater'])
            ->findOrFail($id);
    }

    public function createQuotation(array $data): Quotation
    {

        // Generate next quotation number
        $lastQuotation = Quotation::withTrashed()->latest('created_at')->first();
        $lastNumber = 0;

        if ($lastQuotation && preg_match('/QUO-(\d+)/', $lastQuotation->quotation_number, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        $data['quotation_number'] = "QUO-{$nextNumber}";
        $totals = $this->calculateTotals($data['items'] ?? []);
        $data = array_merge($data, $totals);
        return Quotation::create($data);
    }

    public function updateQuotation(string $id, array $data): Quotation
    {
        $quotation = $this->getQuotationById($id);
        $totals = $this->calculateTotals($data['items'] ?? []);
        $data = array_merge($data, $totals);
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
