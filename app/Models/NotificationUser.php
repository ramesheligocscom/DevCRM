<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationUser extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'notification_user';
    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read',
    ];
}
