<?php

namespace Modules\FollowUp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class FollowUp extends Model
{
    use HasFactory;

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
        'is_deleted'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'last_updated_at' => 'datetime',
        'lead_id' => 'string',
        'client_id' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = \Illuminate\Support\Str::uuid();
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->last_updated_by = auth()->id();
            $model->last_updated_at = now();
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
