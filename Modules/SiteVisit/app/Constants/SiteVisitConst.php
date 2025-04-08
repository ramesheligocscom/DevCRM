<?php

namespace Modules\SiteVisit\Constants;

class SiteVisitConst
{
    const SITE_VISIT_PERMISSION_LIST = [
        # 3. Site Visit Permission
        [
            'name' => 'Site Visit',
            'position' => 3,
            "icon" => 'tabler-antenna-bars-4',
            "category" => [
                [
                    'name' => 'Site Visit List',
                    "permission_list" => [
                        ["name" => 'View Site Visit List', "action" => "siteVisit", "slug" => 'view'],
                        ["name" => 'Create new Site Visit', "action" => "siteVisit", "slug" => 'create'],
                        ["name" => 'Edit Site Visit', "action" => "siteVisit", "slug" => 'edit'],
                        ["name" => 'Export Site Visit list', "action" => "siteVisit", "slug" => 'export-list'],
                        ["name" => 'Assign To', "action" => "siteVisit", "slug" => 'assign-to'],
                        ["name" => 'Delete', "action" => "siteVisit", "slug" => 'delete'],
                        ["name" => 'View info Site Visit Item', "action" => "siteVisit", "slug" => 'show'],
                    ]
                ],
            ]
        ],
    ];
}
