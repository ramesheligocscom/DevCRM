<?php

namespace Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionType extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'permission_types';

    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function permission_category()
    {
        return $this->hasMany(PermissionCategory::class, 'permission_type_id', 'id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'permission_type_id', 'id');
    }
}
