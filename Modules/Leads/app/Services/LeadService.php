<?php

namespace Modules\Leads\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Leads\Models\Lead;

class LeadService
{
    public function getPaginatedLeads(
        int $perPage = 15,
        bool $withTrashed = false,
        ?string $status = null,
        ?string $assignedUser = null,
        ?string $visitAssigneeUser = null,
        ?string $clientId = null,
        ?string $quotationId = null,
        ?string $contractId = null,
        ?string $invoiceId = null,
        ?string $createdBy = null,
        ?string $lastUpdatedBy = null,
    ): LengthAwarePaginator {

        $query = Lead::query()->when($withTrashed, fn($q) => $q->withTrashed());

        # ✅ Apply custom filtering from the helper
        $query = applyFilteringUser($query, 'assign_user');

        return $query->when($status, fn($q) => $q->where('status', $status))
            ->when($assignedUser, fn($q) => $q->where('assigned_user', $assignedUser))
            ->when($visitAssigneeUser, fn($q) => $q->where('visit_assignee', $visitAssigneeUser))
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->when($quotationId, fn($q) => $q->where('quotation_id', $quotationId))
            ->when($contractId, fn($q) => $q->where('contract_id', $contractId))
            ->when($invoiceId, fn($q) => $q->where('invoice_id', $invoiceId))
            ->when($createdBy, fn($q) => $q->where('created_by', $createdBy))
            ->when($lastUpdatedBy, fn($q) => $q->where('last_updated_by', $lastUpdatedBy))
            ->with(['creator', 'updater', 'assignedUser', 'visitAssignee'])
            ->latest()
            ->paginate($perPage);
    }

    public function getLeadById(string $id): Lead
    {
        return Lead::with(['assignedUser', 'visitAssignee', 'creator', 'updater'])
            ->findOrFail($id);
    }

    public function createLead(array $data): Lead
    {
        return Lead::create($data);
    }

    public function updateLead(string $id, array $data): Lead
    {
        $lead = $this->getLeadById($id);
        $lead->update($data);
        return $lead->fresh();
    }

    public function deleteLead(string $id): void
    {
        $lead = $this->getLeadById($id);
        $lead->delete();
    }
}
