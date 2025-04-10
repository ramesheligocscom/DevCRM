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
        'last_updated_by',
    ];

    protected $casts = [
        'visit_time' => 'datetime',
        'id' => 'string',
        'lead_id' => 'string'
    ];


    public function lead()
    {
        return $this->belongsTo(\Modules\Leads\Models\Lead::class)->withDefault();
    }

    public function assignee()
    {
        return $this->belongsTo(\App\Models\User::class, 'visit_assignee')->withDefault();
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'uuid')->withDefault();
    }

    public function client()
    {
        return $this->belongsTo(\Modules\Clients\Models\Client::class)->withDefault();
    }


    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'last_updated_by','uuid');
    }
    
}
