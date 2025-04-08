<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Constants\RolePermissionConst;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Exception;

class RolePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage and assign roles and permissions.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $option = $this->choice(
            'Choose an option',
            ['Update Role Permissions', 'Create New Roles and Permissions']
        );

        return $this->executeOption($option);
    }

    protected function executeOption($option)
    {
        return match ($option) {
            'Update Role Permissions' => $this->updateRolePermission(),
            'Create New Roles and Permissions' => $this->createNewRolePermission(),
            default => $this->error('Invalid option selected.'),
        };
    }

    protected function updateRolePermission()
    {
        DB::beginTransaction();
        try {
            $this->callSeeders([RoleSeeder::class, PermissionSeeder::class]);
            DB::table('role_permission')->truncate();

            $roles = Role::all();
            $roles->each(fn($role) => $this->assignPermissionsToRole($role));

            User::with('roles')->each(fn($user) => $this->assignRolesAndPermissionsToUser($user));

            DB::commit();
            $this->info('Role permissions updated and reassigned successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->error("Error: {$e->getMessage()}");
        }
    }

    protected function createNewRolePermission()
    {
        $roleList = [];  // Define new roles here
        $permissionList = [];  // Define new permissions here

        DB::beginTransaction();
        try {
            foreach ($roleList as $roleData) {
                $role = Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);
                $this->assignPermissionsToRole($role);
            }

            foreach ($permissionList as $permissionData) {
                $permission = Permission::updateOrCreate(['slug' => $permissionData['slug']], $permissionData);
                $this->assignPermissionsToRoles($permission);
            }

            DB::commit();
            $this->info('Role and permission creation completed successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->error("Error: {$e->getMessage()}");
        }
    }

    protected function assignPermissionsToRole(Role $role)
    {
        $allowedPermissions = $this->getAllowedPermissionsForRole($role->slug);
        $role->syncPermissions($allowedPermissions);
    }

    protected function assignRolesAndPermissionsToUser(User $user)
    {
        $user->roles()->sync($user->roles->pluck('id'));
        $permissions = $user->roles->flatMap(fn($role) => $role->permissions);
        $user->syncPermissions($permissions);
    }

    protected function assignPermissionsToRoles(Permission $permission)
    {
        Role::all()->each(function (Role $role) use ($permission) {
            if ($this->isPermissionAllowed($role->slug, $permission->slug)) {
                DB::table('role_permission')->updateOrInsert([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                ]);
            }
        });
    }

    protected function isPermissionAllowed(string $roleSlug, string $permissionSlug): bool
    {
        $allowedPermissions = match ($roleSlug) {
            RolePermissionConst::SUPER_ADMIN => Permission::pluck('name')->toArray(),
            RolePermissionConst::SUPER_ADMIN_USER => RolePermissionConst::SUPER_ADMIN_USER_PERMISSION,
            RolePermissionConst::COMPANY_OWNER => RolePermissionConst::COMPANY_OWNER_PERMISSION,
            RolePermissionConst::BRANCH_MANAGER => RolePermissionConst::BRANCH_MANAGER_PERMISSION,
            RolePermissionConst::MANAGER => RolePermissionConst::MANAGER_PERMISSION,
            RolePermissionConst::EXECUTIVES => RolePermissionConst::EXECUTIVES_PERMISSION,
            RolePermissionConst::REPRESENTATIVES => RolePermissionConst::REPRESENTATIVES_PERMISSION,
            default => [],
        };

        return in_array($permissionSlug, $allowedPermissions, true);
    }

    protected function getAllowedPermissionsForRole(string $roleSlug): array
    {
        return match ($roleSlug) {
            RolePermissionConst::SUPER_ADMIN => Permission::pluck('name')->toArray(),
            RolePermissionConst::SUPER_ADMIN_USER => Permission::whereIn('name', RolePermissionConst::SUPER_ADMIN_USER_PERMISSION)->pluck('name')->toArray(),
            RolePermissionConst::COMPANY_OWNER => Permission::whereIn('name', RolePermissionConst::COMPANY_OWNER_PERMISSION)->pluck('name')->toArray(),
            RolePermissionConst::BRANCH_MANAGER => Permission::whereIn('name', RolePermissionConst::BRANCH_MANAGER_PERMISSION)->pluck('name')->toArray(),
            RolePermissionConst::MANAGER => Permission::whereIn('name', RolePermissionConst::MANAGER_PERMISSION)->pluck('name')->toArray(),
            RolePermissionConst::EXECUTIVES => Permission::whereIn('name', RolePermissionConst::EXECUTIVES_PERMISSION)->pluck('name')->toArray(),
            RolePermissionConst::REPRESENTATIVES => Permission::whereIn('name', RolePermissionConst::REPRESENTATIVES_PERMISSION)->pluck('name')->toArray(),
            default => [],
        };
    }

    protected function callSeeders(array $seeders)
    {
        foreach ($seeders as $seeder) {
            $this->call($seeder);
        }
    }
}
