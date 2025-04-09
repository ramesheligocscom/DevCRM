<?php

namespace Modules\Quotations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Quotation extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'quotation_number',
        'valid_uptil',
        'quotation_type',
        'title',
        'sub_total',
        'discount',
        'tax',
        'total',
        'status',
        'items',
        'custom_header_text',
        'payment_terms',
        'terms_conditions',
        'lead_id',
        'client_id',
        'contract_id',
        'created_by',
        'last_updated_by'
    ];

    protected $casts = [
        'valid_uptil' => 'date',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'items' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

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
        return $this->belongsTo(\App\Models\User::class, 'last_updated_by' ,'uuid');
    }
}
