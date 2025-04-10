<?php

namespace Modules\Invoices\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Invoices\Models\Invoice;

class InvoiceService
{
    public function getPaginatedInvoices(
        int $perPage = 15,
        bool $withTrashed = false,
        ?string $status = null,
        ?string $clientId = null,
        ?string $contractId = null,
        ?string $quotationId = null,
        ?string $createdBy = null,
        ?string $lastUpdatedBy = null
    ): LengthAwarePaginator {
        $query = Invoice::query()->when($withTrashed, fn($q) => $q->withTrashed());

        # ✅ Apply custom filtering from the helper
        $query = applyFilteringUser($query, 'created_by');

        return $query->when($status, fn($q) => $q->where('status', $status))
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->when($contractId, fn($q) => $q->where('contract_id', $contractId))
            ->when($quotationId, fn($q) => $q->where('quotation_id', $quotationId))
            ->when($createdBy, fn($q) => $q->where('created_by', $createdBy))
            ->when($lastUpdatedBy, fn($q) => $q->where('last_updated_by', $lastUpdatedBy))
            ->with(['creator', 'updater'])
            ->latest()
            ->paginate($perPage);
    }

    public function getInvoiceById(string $id): Invoice
    {
        return Invoice::with(['creator', 'updater'])
            ->findOrFail($id);
    }

    public function createInvoice(array $data): Invoice
    {
        // Generate next invoice number
        $lastInvoice = Invoice::withTrashed()->latest('created_at')->first();
        $lastNumber = 0;

        if ($lastInvoice && preg_match('/QUO-(\d+)/', $lastInvoice->invoice_number, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        $data['invoice_number'] = "QUO-{$nextNumber}";
        // Calculate totals before creation
        $totals = $this->calculateTotals($data['items'] ?? []);
        $data = array_merge($data, $totals);
        return Invoice::create($data);
    }

    public function updateInvoice(string $id, array $data): Invoice
    {
        $invoice = $this->getInvoiceById($id);
        // Calculate totals before creation
        $totals = $this->calculateTotals($data['items'] ?? []);
        $data = array_merge($data, $totals);
        $invoice->update($data);
        return $invoice->fresh();
    }

    public function deleteInvoice(string $id): void
    {
        $invoice = $this->getInvoiceById($id);
        $invoice->delete();
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
