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

        # Always include RolePermissionConst
        $list = [];

        # Define modules to check (class + constant name)
        $optionalModules = [
            ['class' => \Modules\Leads\Constants\LeadConst::class, 'const' => 'LEAD_PAGE_PERMISSION_LIST'],
            ['class' => \Modules\Clients\Constants\ClientConst::class, 'const' => 'CLIENT_PAGE_PERMISSION_LIST'],
            ['class' => \Modules\Invoices\Constants\InvoiceConst::class, 'const' => 'INVOICE_PAGE_PERMISSION_LIST'],
            ['class' => \Modules\Contracts\Constants\ContractConst::class, 'const' => 'CONTRACT_PAGE_PERMISSION_LIST'],
            ['class' => \Modules\Quotations\Constants\QuotationConst::class, 'const' => 'QUOTATION_PAGE_PERMISSION_LIST'],
            ['class' => \Modules\SiteVisit\Constants\SiteVisitConst::class, 'const' => 'SITE_VISIT_PAGE_PERMISSION_LIST'],
            ['class' => \Modules\FollowUp\Constants\FollowUpConst::class, 'const' => 'FOLLOW_UP_PAGE_PERMISSION_LIST'],
        ];

        foreach ($optionalModules as $module) {
            if (class_exists($module['class']) && defined($module['class'] . '::' . $module['const'])) {
                $list = array_merge($list, constant($module['class'] . '::' . $module['const']));
            }
        }

        # Sort by position
        usort($list, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        foreach ($list as $page) {
            foreach ($page['statuses'] as $index => $status) {
                AdminControlConfig::updateOrCreate(
                    [
                        'status_for' => $page['page'],
                        'status_text' => $status['status_text'],
                    ],
                    [
                        'invoice_footer_text' => $status['invoice_footer_text'],
                        'contract_footer_text' => $status['contract_footer_text'],
                        'status_color' => $status['status_color'],
                        'position' =>  (int)$status['position'],
                        'is_predefined' => $status['is_predefined'],
                    ]
                );
            }
        }
    }
}
