<?php

namespace Modules\ProductService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class ProductService extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'product_services';

    protected $fillable = [
        'name',
        'price',
        'attributes',
        'created_by',
        'last_updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'attributes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeSearch($query, $searchTerm)
    {
        $term = strtolower($searchTerm);
        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
        });
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'uuid');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'last_updated_by', 'uuid');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->created_by)) {
                $model->created_by = auth()->user()->uuid ?? null;
            }
        });

        static::updating(function ($model) {
            $model->last_updated_by = auth()->user()->uuid ?? null;
        });
    }
}
