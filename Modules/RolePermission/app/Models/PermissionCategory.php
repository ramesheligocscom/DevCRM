<?php

namespace Modules\RolePermission\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionCategory extends Model
{
    use HasFactory, HasUuids;
    # Specify the table name if it's not the default pluralized form
    protected $table = 'permission_categories';

    # Mass assignable attributes
    protected $fillable = [
        'name',
        'slug',
        'permission_type_id',
    ];

    # Hidden attributes (optional, if needed)
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function permissions(){
        return $this->hasMany(Permission::class,'permission_category_id','id');
    }
 
    public function permissionType()
    {
        return $this->belongsTo(PermissionType::class);
    }
}
