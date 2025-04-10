<?php

namespace Modules\SiteVisit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class SiteVisit extends Model
{
    use HasFactory, SoftDeletes, HasUuids;


    protected $fillable = [
        'uuid',
        'visit_time',
        'visit_assignee',
        'created_by',
        'status',
        'visit_notes',
        'lead_id',
        'client_id',
    ];

    protected $casts = [
        'visit_time' => 'datetime',
        'id' => 'string',
        'lead_id' => 'string',
        'client_id' => 'string'
    ];


    public function lead()
    {
        return $this->belongsTo(\Modules\Clients\Models\Lead::class);
    }

    public function client()
    {
        return $this->belongsTo(\Modules\Clients\Models\Client::class);
    }
}
