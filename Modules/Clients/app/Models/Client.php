<?php

namespace Modules\Clients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory, SoftDeletes, HasUuids;


    protected $fillable = [
        'lead_id',
        'name',
        'contact_person',
        'contact_person_role',
        'email',
        'phone',
        'status',
        'assigned_user',
        'created_by',
        'last_updated_by',
        'is_deleted'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'last_updated_at' => 'datetime'
    ];

    // Custom query scopes
    public function scopeFilterByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWhereAssignedUser($query, $userId)
    {
        return $query->where('assigned_user', $userId);
    }

    // Custom creation method
    public static function createWithAttributes(array $attributes)
    {
        return static::create(array_merge([
            'id' => Str::orderedUuid(), // More efficient for DB indexing
            'is_deleted' => false,
            'created_at' => now(),
        ], $attributes));
    }

    // Custom update method
    public function updateWithAttributes(array $attributes)
    {
        return $this->update(array_merge($attributes, [
            'last_updated_at' => now(),
        ]));
    }

    // Soft delete with tracking
    public function softDelete()
    {
        return $this->update([
            'is_deleted' => true,
            'last_updated_by' => auth()->id(),
            'last_updated_at' => now(),
        ]);
    }

    // Relationship loading
    public function loadRelations()
    {
        return $this->load(['parentClient', 'childClients']);
    }

    // Relationships
    // public function parentClient()
    // {
    //     return $this->belongsTo(Client::class, 'lead_id');
    // }

    // public function childClients()
    // {
    //     return $this->hasMany(Client::class, 'lead_id');
    // }

    // public function assignedUser()
    // {
    //     return $this->belongsTo(User::class, 'assigned_user');
    // }
}
