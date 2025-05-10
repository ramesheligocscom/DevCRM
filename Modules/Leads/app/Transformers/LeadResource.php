<?php

namespace Modules\Leads\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
