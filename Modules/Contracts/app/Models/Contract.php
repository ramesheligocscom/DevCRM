<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Contract extends Model
{
    use SoftDeletes, HasUuids;

    // Changed: Removed is_deleted from fillable
    protected $fillable = [
        'items',
        'start_date',
        'end_date',
        'sub_total',
        'discount',
        'tax',
        'status',
        'client_id',      // Kept as nullable reference
        'quotation_id',   // Kept as nullable reference
        'invoice_id',     // Kept as nullable reference
        'created_by',
        'last_updated_by'
    ];

    protected $casts = [
        'items' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Changed: Simplified status scope (no enum)
    public function scopeFilterByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWhereClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Changed: Removed is_deleted from default attributes
    public static function createWithAttributes(array $attributes)
    {
        return static::create(array_merge([
            'id' => Str::orderedUuid(),
        ], $attributes));
    }

    public function updateWithAttributes(array $attributes)
    {
        return $this->update(array_merge($attributes, [
            'last_updated_by' => auth()->id(),
        ]));
    }

    // Relationships (without foreign constraints)
    public function client()
    {
        return $this->belongsTo(\Modules\Clients\Models\Client::class, 'client_id');
    }

    public function quotation()
    {
        return $this->belongsTo(\Modules\Quotations\Models\Quotation::class, 'quotation_id');
    }

    public function invoice()
    {
        return $this->belongsTo(\Modules\Invoices\Models\Invoice::class, 'invoice_id');
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

    // item object may be 
    // {
    //   "item_id": "uuid or string",
    //   "name": "string",
    //   "description": "string",
    //   "quantity": "decimal",
    //   "unit_price": "decimal",
    //   "tax_rate": "decimal",
    //   "tax_amount": "decimal",
    //   "discount_rate": "decimal",
    //   "discount_amount": "decimal",
    //   "subtotal": "decimal",
    //   "total": "decimal"
    //   "custom_fields": "json" // for module-specific fields
    // }
