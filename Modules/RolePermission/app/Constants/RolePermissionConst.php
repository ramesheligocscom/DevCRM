<?php

namespace Modules\RolePermission\Constants;

class RolePermissionConst
{
    # Roles TODO: Make new Role add slug and make permission 
    const SUPER_ADMIN = 'Super Admin';
    const ADMIN = "Admin";

    const SLUG_SUPER_ADMIN = 'super-admin';
    const SLUG_ADMIN = "admin";

    const SUPER_ADMIN_MESSAGE = "Super Admin Role Permission Not Update!";
    const ADMIN_PERMISSION = [];

    const ROLE_LIST = [
        ['name' => RolePermissionConst::SUPER_ADMIN, "slug" => RolePermissionConst::SLUG_SUPER_ADMIN, 'description' => 'Full access to all system features and settings.', "position" => 0],
        ['name' => RolePermissionConst::ADMIN, "slug" => RolePermissionConst::SLUG_ADMIN, 'description' => 'Manage most system settings and data.', "position" => 1],
    ];

    const PERMISSION_LIST = [
        # 1. Dashboard Permission
        [
            'name' => 'Dashboard',
            'position' => 1,
            "icon" => 'tabler-dashboard',
            "category" => [
                [
                    'name' => 'Dashboard',
                    "permission_list" => [
                        ["name" => "Dashboard", "action" => "dashboard", "slug" => 'view'],
                    ],
                ]
            ],
        ],

        # 2. Lead Permission

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

        # 5. Contracts Permission
        [
            'name' => 'Contracts',
            'position' => 4,
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

        # 6. Users Permission
        [
            'name' => 'Users',
            'position' => 5,
            "icon" => 'tabler-users',
            "category" => [
                [
                    'name' => 'Users',
                    "permission_list" => [
                        ["name" => 'View User List', "action" => "user", "slug" => 'view'],
                        ["name" => 'Add User', "action" => "user", "slug" => 'create'],
                        ["name" => 'Edit User', "action" => "user", "slug" => 'edit'],
                        ["name" => 'Delete User', "action" => "user", "slug" => 'delete'],
                        ["name" => 'View User List Item Info', "action" => "user", "slug" => 'show'],
                    ]
                ],
                [
                    'name' => 'Manage Roles',
                    "permission_list" => [
                        ["name" => 'View Role', "action" => "role", "slug" => 'view'],
                        ["name" => 'Add Role', "action" => "role", "slug" => 'create'],
                        ["name" => 'Edit Role', "action" => "role", "slug" => 'edit'],
                        ["name" => 'Delete Role', "action" => "role", "slug" => 'delete'],
                    ]
                ]
            ]
        ],

        # 7. Alert & Notifications Permission
        [
            'name' => 'Alert & Notifications',
            'position' => 6,
            "icon" => 'tabler-bell-dollar',
            "category" => [
                [
                    'name' => 'Bell Notification',
                    "permission_list" => [
                        ["name" => 'View Notifications', "action" => "notification", "slug" => 'view'],
                        ["name" => 'Delete Notifications', "action" => "notification", "slug" => 'delete'],
                    ]
                ]
            ]
        ],

        # 8. Settings Permission
        [
            'name' => 'Settings',
            'position' => 7,
            "icon" => 'tabler-settings',
            "category" => [
                [
                    'name' => 'General Settings',
                    "permission_list" => [
                        ["name" => 'View General Setting', "action" => "generalSetting", "slug" => 'view'],
                        ["name" => 'Save General Setting', "action" => "generalSetting", "slug" => 'save'],
                    ]
                ],
            ]
        ],
    ];
}
