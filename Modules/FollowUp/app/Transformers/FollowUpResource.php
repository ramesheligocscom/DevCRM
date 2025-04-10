<?php

namespace Modules\FollowUp\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowUpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'call_status' => $this->call_status,
            'lead_prospect' => $this->lead_prospect,
            'call_summary' => $this->call_summary,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at->toDateTimeString(),
            'last_updated_by' => $this->last_updated_by,
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'lead_id' => $this->lead_id,
            'client_id' => $this->client_id,
            'relationships' => [
                'lead' => $this->whenLoaded('lead'),
                'client' => $this->whenLoaded('client'),
                'creator' => $this->whenLoaded('creator'),
                'updater' => $this->whenLoaded('updater')
            ],
            // 'links' => [
            //     'self' => route('follow-ups.show', $this->id),
            //     'lead' => $this->lead_id ? route('leads.show', $this->lead_id) : null,
            //     'client' => $this->client_id ? route('clients.show', $this->client_id) : null
            // ]
        ];
    }
}
