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
            'created_at' => now(),
        ], $attributes));
    }

    // Custom update method
    public function updateWithAttributes(array $attributes)
    {
        return $this->update(array_merge($attributes, [
            'updated_at' => now(),
        ]));
    }

    // Soft delete with tracking
    public function softDelete()
    {
        return $this->update([
            'last_updated_by' => auth()->user()->uuid,
            'updated_at' => now(),
        ]);
    }

    // Relationship loading
    public function loadRelations()
    {
        return $this->load([]);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by' , 'uuid');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'last_updated_by', 'uuid');
    }
    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_user' , 'uuid');
    }
     
}
