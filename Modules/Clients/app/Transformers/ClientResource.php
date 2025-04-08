<?php

namespace Modules\Clients\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'contact_person_role' => $this->contact_person_role,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'assigned_user' => $this->assigned_user,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'last_updated_by' => $this->last_updated_by,
            'parent_lead' => $this->whenLoaded('parentLead'),
            'child_leads' => $this->whenLoaded('childLeads')
        ];
    }
}
