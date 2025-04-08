<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TableHeaderManage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'table_header_manages';

    protected $fillable = ['user_id', 'title', 'table', 'slug', 'headers'];

    protected $casts = ['headers' => 'array'];

    public function user()
    {
        return $this->hasOne(User::class, 'uuid', 'user_id');
    }
}
