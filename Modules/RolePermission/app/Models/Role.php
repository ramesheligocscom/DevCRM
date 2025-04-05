<?php

namespace Modules\RolePermission\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\RolePermission\Database\Factories\RoleFactory;

class Role extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'roles';
    protected $fillable = [
        'name',
        'description',
        'position',
        'status',
        'slug',
        'created_by'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id', 'id', 'uuid');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function hasPermission($permission)
    {
        return $this->permissions->contains('name', $permission);
    }

    public function role_permission_list()
    {
        return $this->hasMany(RolePermission::class, 'role_id', 'id');
    }

    public function user_role()
    {
        return $this->hasMany(UserRole::class, 'role_id', 'id');
    }
}
