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


    const LEAD_PAGE_PERMISSION_LIST = [
        [
            'page' => 'Lead',
            'position' => 1,
            'statuses' => [
                ["status_text" => "No Action", "status_color" => "#6c757d", "position" => 1, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null], // Gray
                ["status_text" => "Follow up", "status_color" => "#ffc107", "position" => 2, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null], // Yellow
                ["status_text" => "Interested", "status_color" => "#17a2b8", "position" => 3, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null], // Cyan
                ["status_text" => "Not Interested", "status_color" => "#dc3545", "position" => 4, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null], // Red
            ]
        ],
    ];
}
