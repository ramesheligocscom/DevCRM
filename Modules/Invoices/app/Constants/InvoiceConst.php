<?php

namespace Modules\Invoices\Constants;

class InvoiceConst
{
    const INVOICE_PERMISSION_LIST = [
        # 7. Invoices Permission
        [
            'name' => 'Invoices',
            'position' => 7,
            "icon" => 'tabler-businessplan',
            "category" => [
                [
                    'name' => 'Invoice List',
                    "permission_list" => [
                        ["name" => 'View Invoice List', "action" => "invoice", "slug" => 'view'],
                        ["name" => 'View Invoice List Item Info', "action" => "invoice", "slug" => 'show'],
                        ["name" => 'Create Invoice', "action" => "invoice", "slug" => 'create'],
                        ["name" => 'Edit Invoice', "action" => "invoice", "slug" => 'edit'],
                        ["name" => 'Delete Invoice', "action" => "invoice", "slug" => 'delete'],
                        ["name" => 'Invoice Export list', "action" => "invoice", "slug" => 'export-list'],
                    ]
                ],
            ]
        ],
    ];
}
