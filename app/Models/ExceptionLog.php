<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ExceptionLog extends Model
{
    use HasFactory, HasUuids;

    const PENDING = "Pending";
    const COMPLETE = "Complete";
    const CANCEL = "Cancel";

    protected $table = 'exception_logs';
    protected $fillable = [
        'status',
        'type',
        'title',
        'error',
        'file_name',
        'line_number',
        'type_count',
        'comment',
        'full_error'
    ];
}
