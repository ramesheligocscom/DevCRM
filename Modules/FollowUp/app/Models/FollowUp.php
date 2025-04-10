<?php

namespace Modules\FollowUp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class FollowUp extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'call_status',
        'lead_prospect',
        'call_summary',
        'created_by',
        'last_updated_by',
        'lead_id',
        'client_id',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'lead_id' => 'string',
        'client_id' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = \Illuminate\Support\Str::uuid();
            $model->created_by = auth()->user()->uuid;
        });

        static::updating(function ($model) {
            $model->last_updated_by = auth()->user()->uuid;
            $model->updated_at = now();
        });
    }

    public function lead()
    {
        return $this->belongsTo(\Modules\Leads\Models\Lead::class)->withDefault();
    
    }

    public function client()
    {
        return $this->belongsTo(\Modules\Clients\Models\Client::class)->withDefault();
    
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'last_updated_by');
    }
}
