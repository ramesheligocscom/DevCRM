<?php

namespace Modules\Clients\Models;

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
        'is_deleted'
    ];

    protected $casts = [
        'visit_time' => 'datetime',
        'is_deleted' => 'boolean',
        'id' => 'string',
        'lead_id' => 'string',
        'client_id' => 'string'
    ];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'visit_assignee');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
