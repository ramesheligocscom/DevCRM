<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Contract extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'items',
        'start_date',
        'end_date',
        'sub_total',
        'discount',
        'tax',
        'total',
        'status',
        'client_id',       
        'quotation_id',    
        'invoice_id',      
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

    public function scopeSearch($query, $searchTerm)
    {
        $term = strtolower($searchTerm);
        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"]);
        });
    }

    // Changed: Simplified status scope (no enum)
    public function scopeFilterByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWhereClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
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

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by' , 'uuid');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'last_updated_by', 'uuid');
    }
}

    // invoice table :
    // invoice_number 
    // title
    // description
    // quotation_id

    // contract table :
    // title
    // description
    // total

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
    //   "attributes": "json" // for module-specific fields
    // }
