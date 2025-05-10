<?php

namespace  Modules\Dashboard\Services;

use Modules\Clients\Models\Client;
use Modules\Leads\Models\Lead;
use Nwidart\Modules\Facades\Module;

class DashboardService
{
    public function getDashboard(): Array {

        return [
            'clients' => Module::has('Clients') ? Client::count() : 0,
            'leads' => Module::has('Leads') ? Lead::count() : 0,
        ];
     }
}
