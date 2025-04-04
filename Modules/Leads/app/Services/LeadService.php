<?php

namespace Modules\Leads\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Leads\Models\Leads;

class LeadService
{
    public function createLead(array $data): Leads
    {
        return Leads::createWithAttributes([
            ...$data,
            'created_by' => Auth::id(),
        ]);
    }

    public function updateLead(Leads $lead, array $data): Lead
    {
        $lead->updateWithAttributes([
            ...$data,
            'last_updated_by' => Auth::id(),
        ]);

        return $lead->fresh();
    }

    public function deleteLead(Leads $lead): void
    {
        $lead->softDelete();
    }

    public function convertToClient(Leads $lead, array $clientData): \Modules\Clients\Client
    {
        $client = \Modules\Clients\Client::createWithAttributes([
            ...$clientData,
            'lead_id' => $lead->id,
            'created_by' => Auth::id(),
        ]);

        $lead->updateWithAttributes([
            'status' => 'converted',
            'client_id' => $client->id,
            'last_updated_by' => Auth::id(),
        ]);

        return $client;
    }
}
