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


    # Quotations page statuses
    const QUOTATION_PAGE_PERMISSION_LIST = [
        [
            'page' => 'Quotation',
            'position' => 3,
            'statuses' => [
                ["status_text" => "Ready For Quotation", "status_color" => "#007bff", "position" => 1, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "Quotation Created", "status_color" => "#17a2b8", "position" => 2, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "Quotation in progress 25 %", "status_color" => "#ffc107", "position" => 3, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "Quotation in progress 50 %", "status_color" => "#ffc107", "position" => 4, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "Quotation in progress 75 %", "status_color" => "#ffc107", "position" => 5, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
                ["status_text" => "Quotation Accepted", "status_color" => "#28a745", "position" => 6, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null], // Green
                ["status_text" => "Quotation Cancelled", "status_color" => "#dc3545", "position" => 7, "is_predefined" => 0, "invoice_footer_text" => null, "contract_footer_text" => null],
            ]
        ],
    ];
}
