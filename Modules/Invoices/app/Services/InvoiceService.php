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
        ?string $clientId = null
    ): LengthAwarePaginator {
        return Invoice::query()
            ->when($withTrashed, fn($q) => $q->withTrashed())
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->with(['client', 'contract', 'creator', 'updater'])
            ->latest()
            ->paginate($perPage);
    }

    public function getInvoiceById(string $id): Invoice
    {
        return Invoice::with(['client', 'contract', 'creator', 'updater'])
            ->findOrFail($id);
    }

    public function createInvoice(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function updateInvoice(string $id, array $data): Invoice
    {
        $invoice = $this->getInvoiceById($id);
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
