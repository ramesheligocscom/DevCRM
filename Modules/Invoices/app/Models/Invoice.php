<?php

namespace Modules\Invoices\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'items',
        'sub_total',
        'tax',
        'discount',
        'total',
        'status',
        'client_id',
        'contract_id',
        'created_by',
        'last_updated_by'
    ];

    protected $casts = [
        'items' => 'array',
        'sub_total' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeFilterByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public static function createWithAttributes(array $attributes)
    {
        return static::create(array_merge([
            'id' => Str::orderedUuid(),
        ], $attributes));
    }

    public function updateWithAttributes(array $attributes)
    {
        return $this->update(array_merge($attributes, [
            'last_updated_by' => auth()->user()->uuid,
        ]));
    }

    public function client()
    {
        return $this->belongsTo(\Modules\Clients\Models\Client::class, 'client_id');
    }

    public function contract()
    {
        return $this->belongsTo(\Modules\Contracts\Models\Contract::class, 'contract_id');
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
