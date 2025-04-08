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
        # 3. Site Visit Permission
        # 3. Clients Permission
        # 4. Quotations Permission
        # 5. Contracts Permission
        # 7. Invoices Permission
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
                        ["name" => 'Add Role', "action" => "role", "slug" => 'create'],
                        ["name" => 'View Role', "action" => "role", "slug" => 'view'],
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
