<?php

namespace Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'permissions';

    protected $fillable = [
        'title',
        'slug',
        'action',
        'permission',
        'description',
        'permission_type_id',
        'permission_category_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function permission_category()
    {
        return $this->hasOne(PermissionCategory::class, 'permission_category_id', 'id');
    }

    public function permission_type()
    {
        return $this->hasOne(PermissionType::class, 'permission_type_id', 'id');
    }
}
