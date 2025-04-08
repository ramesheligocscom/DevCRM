<?php

namespace Modules\Leads\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Leads\Models\Lead;
use Modules\Clients\Models\Client;

class LeadService
{
    public function getPaginatedLeads(
        int $perPage = 15,
        bool $withTrashed = false,
        ?string $status = null,
        ?string $assignedUser = null
    ): LengthAwarePaginator {

        return Lead::query()
            ->when($withTrashed, fn($q) => $q->withTrashed())
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($assignedUser, fn($q) => $q->where('assigned_user', $assignedUser))
            ->with(['client', 'quotation', 'contract', 'invoice', 'visitAssignee', 'creator', 'updater'])
            ->latest()
            ->paginate($perPage);
    }

    public function getLeadById(string $id): Lead
    {
        return Lead::with(['client', 'quotation', 'contract', 'invoice', 'visitAssignee','creator','updater'])
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

    public function convertToClient(string $leadId, array $clientData): \Modules\Clients\Models\Client
    {
        $lead = $this->getLeadById($leadId);
        
        $client = \Modules\Clients\Models\Client::create(
            array_merge($clientData, ['lead_id' => $leadId])
        );

        $lead->update([
            'status' => 'converted',
            'client_id' => $client->id,
            'last_updated_by' => auth()->user()->uuid
        ]);

        return $client;
    }

    public function getPaginatedClients(
        int $perPage = 15,
        bool $withTrashed = false,
        ?string $status = null,
        ?string $leadId = null
    ): LengthAwarePaginator {
        return Client::query()
            ->when($withTrashed, fn($q) => $q->withTrashed())
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($leadId, fn($q) => $q->where('lead_id', $leadId))
            ->latest()
            ->paginate($perPage);
    }
}

