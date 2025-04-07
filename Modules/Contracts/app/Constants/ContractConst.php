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
}
