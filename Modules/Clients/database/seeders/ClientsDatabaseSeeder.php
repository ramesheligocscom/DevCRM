<?php

namespace Modules\Clients\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClientsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PostgreSQL doesn't have FOREIGN_KEY_CHECKS, so we disable constraints differently
        Schema::disableForeignKeyConstraints();
        DB::table('clients')->truncate();
        Schema::enableForeignKeyConstraints();

        $statuses = ['active', 'inactive', 'pending', 'onboarding'];
        $roles = ['CEO', 'CTO', 'Manager', 'Director', 'Owner'];
        $industries = ['Technology', 'Finance', 'Healthcare', 'Retail', 'Manufacturing'];

        $clients = [];

        for ($i = 0; $i < 50; $i++) {
            $name = fake()->company();
            $contactPerson = fake()->name();

            $clients[] = [
                'id' => Str::uuid(),
                'lead_id' => null, // You can set this if you have leads seeded
                'name' => $name,
                'contact_person' => $contactPerson,
                'contact_person_role' => fake()->randomElement($roles),
                'email' => 'contact@' . Str::slug($name) . '.com',
                'phone' => fake()->phoneNumber(),
                'status' => fake()->randomElement($statuses),
                'assigned_user' => 'user' . ($i % 5 + 1) . '@example.com',
                'updated_at' => now()->subYear(),
                'created_at' => now()->subDays(rand(1, 365)),
                'created_by' => 'admin@example.com',
                'last_updated_by' => rand(0, 1) ? 'admin@example.com' : null,
            ];
        }

        // Insert in chunks for better performance
        foreach (array_chunk($clients, 100) as $chunk) {
            DB::table('clients')->insert($chunk);
        }

        // Add some specific test cases
        DB::table('clients')->insert([
            'id' => Str::uuid(),
            'name' => 'ACME Corporation',
            'contact_person' => 'John Doe',
            'contact_person_role' => 'CEO',
            'email' => 'john@acme.com',
            'phone' => '+1 555-123-4567',
            'status' => 'active',
            'assigned_user' => 'user1@example.com',
            'updated_at' => now()->subYear(),
            'created_at' => now()->subYear(),
            'created_by' => 'admin@example.com',
            'last_updated_by' => 'user1@example.com',
        ]);
    }
}
