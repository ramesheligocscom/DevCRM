<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\RolePermission\Constants\RolePermissionConst;
use Illuminate\Support\Facades\DB;
use Modules\RolePermission\Models\Role;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roles = RolePermissionConst::ROLE_LIST;
        $users = [
            [
                'name' => 'Pankaj Sharma',
                'email' => 'admin@eligocs.com',
                'user_name' => 'super_admin',
                'password' =>  'qwerty123',
                'avatar' => null,
                'status' => User::ACTIVE,
                'email_verified_at' => now()->format('Y-m-d H:i:s'),
                "roles" => [RolePermissionConst::SLUG_SUPER_ADMIN],
            ],
            [
                'name' => 'Admin',
                'email' => 'admin1@eligocs.com',
                'user_name' => 'admin',
                'password' => 'qwerty123',
                'avatar' => null,
                'status' => User::ACTIVE,
                'email_verified_at' => now()->format('Y-m-d H:i:s'),
                "roles" => [RolePermissionConst::SLUG_ADMIN],
            ],
        ];

        # Create or update roles with a progress bar
        $output = new ConsoleOutput();
        $output->writeln('Seeding users and assigning roles...');
        $progressBar = new ProgressBar($output, count($users));
        $progressBar->start();

        foreach ($users as $userData) {
            # Find roles by their names
            $roleIds = Role::whereIn('slug', $userData['roles'])->pluck('id')->toArray();

            # Create or update the user
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'user_name' => $userData['user_name'],
                    'password' => Hash::make($userData['password']),
                    'avatar' => $userData['avatar'] ?? null,
                    'status' => $userData['status'],
                    'email_verified_at' => $userData['email_verified_at'],
                ]
            );

            # Remove old roles and assign new ones
            $user->roles()->sync($roleIds);
            $progressBar->advance();
        }

        $progressBar->finish();
        $output->writeln("\nUsers seeded and roles assigned successfully!");
    }
}
