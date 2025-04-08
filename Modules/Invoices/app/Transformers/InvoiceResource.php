<?php

namespace Modules\Invoices\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);

        // return [
        //     'id' => $this->id,
        //     'items' => $this->items,
        //     'sub_total' => (float) $this->sub_total,
        //     'tax' => (float) $this->tax,
        //     'discount' => (float) $this->discount,
        //     'total' => (float) $this->total,
        //     'status' => $this->status,
        //     'client_id' => $this->client_id,
        //     'contract_id' => $this->contract_id,
        //     'created_at' => $this->created_at->toDateTimeString(),
        //     'created_by' => $this->created_by,
        //     'updated_at' => $this->updated_at->toDateTimeString(),
        //     'last_updated_by' => $this->last_updated_by,
        //     'deleted_at' => $this->whenNotNull($this->deleted_at?->toDateTimeString()),
        //     'client' => $this->whenLoaded('client'),
        //     'contract' => $this->whenLoaded('contract'),
        //     'creator' => $this->whenLoaded('creator'),
        //     'updater' => $this->whenLoaded('updater')
        // ];
    }
}
