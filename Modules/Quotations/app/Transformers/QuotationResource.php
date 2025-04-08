<?php

namespace Modules\Quotations\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return parent::toArray($request);

        // return [
        //     'id' => $this->id,
        //     'quotation_number' => $this->quotation_number,
        //     'valid_uptil' => $this->valid_uptil->format('Y-m-d'),
        //     'quotation_type' => $this->quotation_type,
        //     'title' => $this->title,
        //     'sub_total' => (float) $this->sub_total,
        //     'discount' => (float) $this->discount,
        //     'tax' => (float) $this->tax,
        //     'total' => (float) $this->total,
        //     'status' => $this->status,
        //     'items' => $this->items,
        //     'custom_header_text' => $this->custom_header_text,
        //     'payment_terms' => $this->payment_terms,
        //     'terms_conditions' => $this->terms_conditions,
        //     'lead_id' => $this->lead_id,
        //     'client_id' => $this->client_id,
        //     'contract_id' => $this->contract_id,
        //     'created_at' => $this->created_at->toDateTimeString(),
        //     'created_by' => $this->created_by,
        //     'updated_at' => $this->updated_at->toDateTimeString(),
        //     'last_updated_by' => $this->last_updated_by,
        //     'deleted_at' => $this->whenNotNull($this->deleted_at?->toDateTimeString()),
        //     'lead' => $this->whenLoaded('lead'),
        //     'client' => $this->whenLoaded('client'),
        //     'contract' => $this->whenLoaded('contract'),
        //     'creator' => $this->whenLoaded('creator'),
        //     'updater' => $this->whenLoaded('updater')
        // ];
    }
}
