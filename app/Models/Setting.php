<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Setting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['key', 'value', 'created_by', 'last_updated_by'];
}
