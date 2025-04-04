<?php

namespace Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Leads extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'contact_person',
        'contact_person_role',
        'email',
        'phone',
        'address',
        'status',
        'source',
        'assigned_user',
        'note',
        'visit_assignee',
        'visit_time',
        'created_by',
        'last_updated_by',
        'client_id',
        'quotation_id',
        'contract_id',
        'invoice_id',
        'is_deleted'
    ];

    protected $casts = [
        'visit_time' => 'datetime',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'last_updated_at' => 'datetime'
    ];

    public function scopeFilterByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWhereAssignedUser($query, $userId)
    {
        return $query->where('assigned_user', $userId);
    }

    public static function createWithAttributes(array $attributes)
    {
        return static::create(array_merge([
            'id' => Str::orderedUuid(),
            'is_deleted' => false,
            'created_at' => now(),
        ], $attributes));
    }

    public function updateWithAttributes(array $attributes)
    {
        return $this->update(array_merge($attributes, [
            'last_updated_at' => now(),
        ]));
    }

    public function softDelete()
    {
        return $this->update([
            'is_deleted' => true,
            'last_updated_by' => auth()->id(),
            'last_updated_at' => now(),
        ]);
    }

    public function loadRelations()
    {
        return $this->load(['client', 'quotation', 'contract', 'invoice']);
    }

    public function client()
    {
        return $this->belongsTo(\Modules\Clients\Client::class);
    }

    public function quotation()
    {
        return $this->belongsTo(\Modules\Quotations\Quotation::class);
    }

    public function contract()
    {
        return $this->belongsTo(\Modules\Contracts\Contract::class);
    }

    public function invoice()
    {
        return $this->belongsTo(\Modules\Invoices\Invoice::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_user');
    }

    public function visitAssignee()
    {
        return $this->belongsTo(\App\Models\User::class, 'visit_assignee');
    }
}
