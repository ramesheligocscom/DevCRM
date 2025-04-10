<?php

namespace Modules\SiteVisit\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteVisitResource extends JsonResource
{
    public function toArray($request)
    {
        // dd($this);
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'visit_time' => $this->visit_time->toDateTimeString(),
            'visit_assignee' => $this->visit_assignee,
            'assignee_name' => $this->whenLoaded('assignee', function () {
                return $this->assignee->name;
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'created_by' => $this->created_by,
            'creator_name' => $this->whenLoaded('creator', function () {
                return $this->creator->name;
            }),
            'status' => $this->status,
            'visit_notes' => $this->visit_notes,
            'lead_id' => $this->lead_id,
            'lead_name' => $this->whenLoaded('lead', function () {
                return $this->lead->name;
            }),
            'client_id' => $this->client_id,
            'client_name' => $this->whenLoaded('client', function () {
                return $this->client->name;
            }),
        ];
    }
}
