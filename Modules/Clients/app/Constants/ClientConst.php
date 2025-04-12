<?php

namespace Modules\Clients\Constants;

class ClientConst
{
    const CLIENT_PERMISSION_LIST = [
        # 3. Clients Permission
        [
            'name' => 'Clients',
            'position' => 3,
            "icon" => 'tabler-hierarchy-2',
            "category" => [
                [
                    'name' => 'Clients',
                    "permission_list" => [
                        ["name" => 'View Client', "action" => "client", "slug" => 'view'],
                        ["name" => 'Create Client', "action" => "client", "slug" => 'create'],
                        ["name" => 'Edit Client', "action" => "client", "slug" => 'edit'],
                        ["name" => 'Assign To Client', "action" => "client", "slug" => 'assign-to'],
                        ["name" => 'Client Export list', "action" => "client", "slug" => 'export-list'],
                        ["name" => 'Status Update Client', "action" => "client", "slug" => 'status-update'],
                        ["name" => 'Delete Client', "action" => "client", "slug" => 'delete'],
                        ["name" => 'View info Client list Item', "action" => "client", "slug" => 'show'],
                    ]
                ],
            ]
        ],
    ];

    # Clients page statuses
    const CLIENT_PAGE_PERMISSION_LIST = [
        [
            'page' => 'Clients',
            'position' => 5,
            'statuses' => [
                ["status_text" => "Active", "status_color" => "#28a745", "position" => 1, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "In Active", "status_color" => "#6c757d", "position" => 2, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
            ]
        ],
    ];
}
