<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminControlConfig;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pageStatuses = [
            # Lead page statuses
            ['page' => 'Lead', 'statuses' => [
                "No Action",
                "Follow up",
                "Interested",
                "Not Interested",
                "Ready For SRM",
                "Ready For Quotation",
                "Quotation Created",
                "Quotation in progress 25 %",
                "Quotation in progress 50 %",
                "Quotation in progress 75 %",
                "Quotation Accepted",
                "Quotation Cancelled"
            ]],

            # Contracts page statuses
            ['page' => 'Contracts', 'statuses' => ['Active', 'In Active', 'Expired', 'Renew']],

            # Clients page statuses
            ['page' => 'Clients', 'statuses' => ['Active', 'In Active']],

            # Invoice page statuses
            ['page' => 'Invoices', 'statuses' => ['paid', 'pending', 'partial']],
        ];

        foreach ($pageStatuses as $page) {
            foreach ($page['statuses'] as $index => $status) {
                AdminControlConfig::updateOrCreate(
                    [
                        'status_for' => $page['page'],
                        'status_text' => $status,
                    ],
                    [
                        'invoice_footer_text' => null,
                        'contract_footer_text' => null,
                        'status_color' => null,
                        'position' => $index + 1,
                        'is_predefined' => 0,
                    ]
                );
            }
        }
    }
}
