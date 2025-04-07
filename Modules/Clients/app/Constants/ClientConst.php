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
                    ]
                ],
                [
                    'name' => 'View Client List Items',
                    "permission_list" => [
                        ["name" => 'View info Client list Item', "action" => "client", "slug" => 'show'],
                        ["name" => 'Follow up', "action" => "client", "slug" => 'follow-up'],
                        ["name" => 'Create Follow Up', "action" => "client", "slug" => 'create-follow-up'],
                        ["name" => 'Edit Follow Up', "action" => "client", "slug" => 'edit-follow-up'],
                        ["name" => 'Activity Timeline', "action" => "client", "slug" => 'activity-timeline'],

                        ["name" => 'View Site Risk Management', "action" => "client", "slug" => 'view-site-risk-management'],
                        ["name" => 'View info Item Site Risk Management', "action" => "client", "slug" => 'show-site-risk-management'],
                        ["name" => 'Create Site Risk Management', "action" => "client", "slug" => 'create-site-risk-management'],
                        ["name" => 'Edit Site Risk Management', "action" => "client", "slug" => 'edit-site-risk-management'],
                        ["name" => 'Delete Site Risk Management', "action" => "client", "slug" => 'delete-site-risk-management'],

                        ["name" => 'View Contract', "action" => "client", "slug" => 'view-contract'],
                        ["name" => 'View info Item Contract', "action" => "client", "slug" => 'show-contract'],
                        ["name" => 'Create Contract', "action" => "client", "slug" => 'create-contract'],
                        ["name" => 'Edit Contract', "action" => "client", "slug" => 'edit-contract'],
                        ["name" => 'Delete Contract', "action" => "client", "slug" => 'delete-contract'],
                    ]
                ]
            ]
        ],
    ];
}
