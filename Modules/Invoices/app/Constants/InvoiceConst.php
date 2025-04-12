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

    # Invoice page statuses
    const INVOICE_PAGE_PERMISSION_LIST = [
        [
            'page' => 'Invoices',
            'position' => 4,
            'statuses' => [
                ["status_text" => "Paid", "status_color" => "#28a745", "position" => 1, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "Pending", "status_color" => "#ffc107", "position" => 2, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "Partial", "status_color" => "#17a2b8", "position" => 3, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
            ]
        ],
    ];
}
