<?php

namespace Modules\Leads\Constants;

class LeadConst
{
    const LEAD_PERMISSION_LIST = [
        # 2. Lead Permission
        [
            'name' => 'Leads',
            'position' => 2,
            "icon" => 'tabler-antenna-bars-4',
            "category" => [
                [
                    'name' => 'Leads',
                    "permission_list" => [
                        ["name" => 'View Lead List', "action" => "leads", "slug" => 'view'],
                        ["name" => 'Create new Lead', "action" => "leads", "slug" => 'create'],
                        ["name" => 'Edit Lead', "action" => "leads", "slug" => 'edit'],
                        ["name" => 'Export Lead list', "action" => "leads", "slug" => 'export-list'],
                        ["name" => 'Assign To Lead', "action" => "leads", "slug" => 'assign-to'],
                        ["name" => 'Status Update Lead', "action" => "leads", "slug" => 'status-update'],
                        ["name" => 'Delete Lead', "action" => "leads", "slug" => 'delete'],
                        ["name" => 'View info Leads Item ', "action" => "leads", "slug" => 'show'],
                    ]
                ],
            ]
        ],
    ];
}
