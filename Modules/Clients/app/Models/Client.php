<?php

namespace Modules\Clients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Clients\Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $fillable = [
        'lead_id',
        'name',
        'contact_person',
        'contact_person_role',
        'email',
        'phone',
        'status',
        'assigned_user',
        'created_by',
        'last_updated_by',
        'is_deleted'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'last_updated_at' => 'datetime'
    ];

    // Relationship with parent lead if exists
    // public function parentLead()
    // {
    //     return $this->belongsTo(Lead::class, 'lead_id');
    // }

    // // Relationship with child leads if exists
    // public function childLeads()
    // {
    //     return $this->hasMany(Lead::class, 'lead_id');
    // }
}
