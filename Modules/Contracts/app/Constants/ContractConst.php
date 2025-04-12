<?php

namespace Modules\Contracts\Constants;

class ContractConst
{
    const CONTRACT_PERMISSION_LIST = [
        # 5. Contracts Permission
        [
            'name' => 'Contracts',
            'position' => 5,
            "icon" => 'tabler-businessplan',
            "category" => [
                [
                    'name' => 'Contracts List',
                    "permission_list" => [
                        ["name" => 'View Contract', "action" => "contract", "slug" => 'view'],
                        ["name" => 'View Contract List Item Info', "action" => "contract", "slug" => 'show'],
                        ["name" => 'Create Contract', "action" => "contract", "slug" => 'create'],
                        ["name" => 'Edit Contract', "action" => "contract", "slug" => 'edit'],
                        ["name" => 'Delete Contract', "action" => "contract", "slug" => 'delete'],
                        ["name" => 'Contract Export list', "action" => "contract", "slug" => 'export-list'],
                    ]
                ],
            ]
        ],
    ];


    # Contracts page statuses
    const CONTRACT_PAGE_PERMISSION_LIST = [
        [
            'page' => 'Contracts',
            'position' => 6,
            'statuses' => [
                ["status_text" => "Active", "status_color" => "#28a745", "position" => 1, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "In Active", "status_color" => "#6c757d", "position" => 2, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "Expired", "status_color" => "#dc3545", "position" => 3, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "Renew", "status_color" => "#007bff", "position" => 4, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
            ]
        ],
    ];
}
