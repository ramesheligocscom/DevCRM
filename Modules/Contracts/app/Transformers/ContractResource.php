<?php

namespace Modules\Contracts\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);

        // return [
        //     'id' => $this->id,
        //     'items' => $this->items,
        //     'start_date' => $this->start_date->format('Y-m-d'),
        //     'end_date' => $this->end_date->format('Y-m-d'),
        //     'sub_total' => (float) $this->sub_total,
        //     'discount' => (float) $this->discount,
        //     'tax' => (float) $this->tax,
        //     'total' => (float) ($this->sub_total + $this->tax - $this->discount),
        //     'status' => $this->status,
        //     // Changed: Removed exists validation for these references
        //     'client_id' => $this->client_id,
        //     'quotation_id' => $this->quotation_id,
        //     'invoice_id' => $this->invoice_id,
        //     'created_by' => $this->created_by,
        //     'created_at' => $this->created_at->toDateTimeString(),
        //     'updated_at' => $this->updated_at->toDateTimeString(),
        //     'last_updated_by' => $this->last_updated_by,
        //     'client' => $this->whenLoaded('client'),
        //     'quotation' => $this->whenLoaded('quotation'),
        //     'invoice' => $this->whenLoaded('invoice'),
        //     'creator' => $this->whenLoaded('creator'),
        //     'updater' => $this->whenLoaded('updater'),
        //     'deleted_at' => $this->whenNotNull($this->deleted_at?->toDateTimeString())
        // ];
    }
}
