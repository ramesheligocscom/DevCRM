<?php

namespace Modules\Quotations\Constants;

class QuotationConst
{
    const QUOTATION_PERMISSION_LIST = [
        # 4. Quotations Permission
        [
            'name' => 'Quotations',
            'position' => 4,
            "icon" => 'tabler-users',
            "category" => [
                [
                    'name' => 'Quotation list',
                    "permission_list" => [
                        ["name" => 'View Quotation', "action" => "quotation", "slug" => 'view'],
                        ["name" => 'View Quotation List Item Info', "action" => "quotation", "slug" => 'show'],
                        ["name" => 'Create Quotation', "action" => "quotation", "slug" => 'create'],
                        ["name" => 'Edit Quotation', "action" => "quotation", "slug" => 'edit'],
                        ["name" => 'Delete Quotation', "action" => "quotation", "slug" => 'delete'],
                        ["name" => 'Quotation Export list', "action" => "quotation", "slug" => 'export-list'],
                    ]
                ],
            ]
        ],
    ];
}
