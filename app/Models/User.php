<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Modules\RolePermission\Models\Role;
use Modules\RolePermission\Models\UserRole;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    const USER_IMAGE = 'user_image';

    const SUPER_ADMIN = 'Super Admin';
    const ADMIN = 'Admin';

    const ACTIVE = 'Active';
    const IN_ACTIVE = 'In-Active';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'user_name',
        'password',
        'avatar',
        'status',
        'email_verified_at',
        'uuid'
    ];

    // Boot method to generate UUID
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            do {
                $uuid = (string) Str::uuid();
            } while (User::where('uuid', $uuid)->exists());

            $user->uuid = $uuid;
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id', 'uuid', 'id');
    }

    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    public function user_role()
    {
        return $this->hasMany(UserRole::class, 'user_id', 'uuid');
    }

    public function getPermissionsViaRoles()
    {
        return $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }
}
