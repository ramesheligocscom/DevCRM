<?php

namespace Modules\RolePermission\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\RolePermission\Models\Permission;
use Modules\RolePermission\Models\PermissionCategory;
use Modules\RolePermission\Models\PermissionType;
use Illuminate\Support\Str;
use Modules\RolePermission\Constants\RolePermissionConst;
use Modules\RolePermission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        # Always include RolePermissionConst
        $permission_list = RolePermissionConst::PERMISSION_LIST;

        # Define modules to check (class + constant name)
        $optionalModules = [
            ['class' => \Modules\Leads\Constants\LeadConst::class, 'const' => 'LEAD_PERMISSION_LIST'],
            ['class' => \Modules\Booking\Constants\BookingConst::class, 'const' => 'BOOKING_PERMISSION_LIST'],
        ];

        foreach ($optionalModules as $module) {
            if (class_exists($module['class']) && defined($module['class'] . '::' . $module['const'])) {
                $permission_list = array_merge($permission_list, constant($module['class'] . '::' . $module['const']));
            }
        }

        # Sort by position
        usort($permission_list, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        # Clean up old data
        DB::table('role_permissions')->whereNotNull('role_id')->delete();
        Permission::whereNotNull('title')->delete();
        PermissionCategory::whereNotNull('name')->delete();
        PermissionType::whereNotNull('name')->delete();
        $total_permission = 0;
        foreach ($permission_list as $type) {
            $total_permission++;
            foreach ($type['category'] as $category) {
                $total_permission++;
                foreach ($category['permission_list'] as $permission) {
                    $total_permission++;
                }
            }
        }

        $progressBar = $this->command->getOutput()->createProgressBar($total_permission);
        $progressBar->start();

        foreach ($permission_list as $type) {
            $permission_type = PermissionType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'slug' => Str::slug($type['name']),
                    'icon' => $type['icon'],
                ]
            );
            $progressBar->advance();
            foreach ($type['category'] as $category) {
                $permission_category = PermissionCategory::updateOrCreate(
                    ['permission_type_id' => $permission_type->id, 'name' => $category['name']],
                    [
                        'slug' => Str::slug($category['name']),
                        'permission_type_id' => $permission_type->id,
                    ]
                );
                $progressBar->advance();
                foreach ($category['permission_list'] as $permission) {
                    $full_permission = $permission['action'] . '_' . $permission['slug'];

                    Permission::updateOrCreate(
                        [
                            'permission_type_id' => $permission_type->id,
                            'permission_category_id' => $permission_category->id,
                            'title' => $permission['name'],
                        ],
                        [
                            'permission' => $full_permission,
                            'slug' => $permission['slug'],
                            'action' => $permission['action'],
                            'description' => $permission['name'] . ' description',
                            'permission_type_id' => $permission_type->id,
                            'permission_category_id' => $permission_category->id,
                        ]
                    );
                    $progressBar->advance();
                }
            }
        }

        $progressBar->finish();
        $this->command->info("\nPermission seeded successfully!");

        # Role Permission assign
        $role_list = Role::get();
        foreach ($role_list as $role) {
            $permissions_ids = Permission::pluck('id')->toArray();
            # remove move old permission assign in $role  
            switch ($role->slug) {
                case RolePermissionConst::SLUG_SUPER_ADMIN:
                    $permissions_ids = Permission::pluck('id')->toArray();
                    break;
                case RolePermissionConst::SLUG_ADMIN:
                    $permissions_ids = Permission::whereIn('permission', RolePermissionConst::ADMIN_PERMISSION)->pluck('id')->toArray();
                    break;
                default:
                    $permissions_ids = [];
                    break;
            }
            $role->permissions()->sync($permissions_ids);
        }
    }
}
