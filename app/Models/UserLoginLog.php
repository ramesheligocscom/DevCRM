<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    use HasUuids;

    protected $table = 'user_login_logs';
    protected $fillable = [
        'user_id',
        'ip_address',
        'country',
        'state',
        'city',
        'user_agent',
        'event',
        'success',
        'logged_at'
    ];
    public $timestamps = false;
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // $model->id = Str::uuid();
        });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'user_id');
    }
}
