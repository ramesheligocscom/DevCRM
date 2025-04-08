<?php

namespace Modules\RolePermission\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\RolePermission\Constants\RolePermissionConst;
use Modules\RolePermission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        # role Create
        $roles = RolePermissionConst::ROLE_LIST;

        # Start the progress bar
        $progressBar = $this->command->getOutput()->createProgressBar(count($roles));
        $progressBar->start();
        foreach ($roles as $role) {
            $role = Role::updateOrCreate(['name' => $role['name']], [
                'name' => $role['name'],
                'slug' => $role['slug'],
                "position" => $role['position'],
                'description' => $role['description'],
            ]);
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->command->info("\nRoles seeded successfully!");
    }
}
