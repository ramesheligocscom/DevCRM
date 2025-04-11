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

    public function scopeSearch($query, $searchTerm)
    {
        $term = strtolower($searchTerm);
        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(visit_notes) LIKE ?', ["%{$term}%"]);
        });
    }
    
    public function assignee()
    {
        return $this->belongsTo(\App\Models\User::class, 'visit_assignee', 'uuid');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'uuid');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'last_updated_by','uuid');
    }
    
}
