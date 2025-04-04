<?php

namespace Modules\Leads\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{

    

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'contact_person_role' => $this->contact_person_role,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'source' => $this->source,
            'assigned_user' => $this->assigned_user,
            'note' => $this->note,
            'visit_assignee' => $this->visit_assignee,
            'visit_time' => $this->visit_time?->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'created_by' => $this->created_by,
            'last_updated_at' => $this->last_updated_at?->toDateTimeString(),
            'last_updated_by' => $this->last_updated_by,
            'client_id' => $this->client_id,
            'quotation_id' => $this->quotation_id,
            'contract_id' => $this->contract_id,
            'invoice_id' => $this->invoice_id,
            'client' => $this->whenLoaded('client'),
            'quotation' => $this->whenLoaded('quotation'),
            'contract' => $this->whenLoaded('contract'),
            'invoice' => $this->whenLoaded('invoice'),
            'assigned_user_detail' => $this->whenLoaded('assignedUser'),
            'visit_assignee_detail' => $this->whenLoaded('visitAssignee')
        ];
    }
}
