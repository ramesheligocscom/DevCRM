<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminControlConfig extends Model
{
    use HasFactory, HasUuids;
    protected $table = "admin_control_configs";
    protected $fillable = [
        'invoice_footer_text',
        'contract_footer_text',
        'status_for',
        'status_text',
        'status_color',
        'position',
        'is_predefined',
    ];
}
