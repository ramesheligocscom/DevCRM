<?php

namespace Modules\SiteVisit\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteVisitResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
