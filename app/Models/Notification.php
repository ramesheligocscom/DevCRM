<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Nwidart\Modules\Facades\Module;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'notifications';
    protected $fillable = [
        'title',
        'module_type',
        'message',
        'user_id',
        'module_id',
        'show_ids',
        'event_ids'
    ];

    protected $casts = [
        'show_ids' => 'array',
        'event_ids' => 'array',
    ];

    public function scopeSearch($query, $filter)
    {
        if (!empty($filter['search'])) {
            $search = $filter['search'];

            $query->where(function ($que) use ($search) {
                $que->where('title', 'like', "%{$search}%")->orWhere('module_type', 'like', "%{$search}%")
                    ->orWhereHas('creator', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('lead', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'uuid', 'user_id');
    }

    public function lead()
    {
        if (Module::has('Leads')) {
            return $this->hasOne(\Modules\Leads\Models\Leads::class, 'id', 'module_id');
        } else {
            return null;
        }
    }

    public function client()
    {
        if (Module::has('Clients')) {
            return $this->hasOne(\Modules\Clients\Models\Client::class, 'id', 'module_id');
        } else {
            return null;
        }
    }

    public function quotation()
    {
        if (Module::has('Quotation')) {
            return $this->hasOne(\Modules\Quotation\Models\Quotation::class, 'id', 'module_id');
        } else {
            return null;
        }
    }

    public function srm()
    {
        if (Module::has('SiteVisit')) {
            return $this->hasOne(\Modules\SiteVisit\Models\SiteVisit::class, 'id', 'module_id');
        } else {
            return null;
        }
    }

    public function contract()
    {
        if (Module::has('Contract')) {
            return $this->hasOne(\Modules\Contract\Models\Contract::class, 'id', 'module_id');
        } else {
            return null;
        }
    }

    public function schedule()
    {
        if (Module::has('Scheduling')) {
            return $this->hasOne(\Modules\Scheduling\Models\ServiceScheduling::class, 'id', 'module_id');
        } else {
            return null;
        }
    }

    public function read()
    {
        return $this->hasOne(NotificationUser::class, 'notification_id', 'id');
    }

    public function reads()
    {
        return $this->hasMany(NotificationUser::class, 'notification_id', 'id');
    }
}
