<?php

namespace Modules\Clients\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Clients\app\Models\Client; // Add this import
use Illuminate\Support\Str;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $statuses = ['active', 'inactive', 'prospect', 'archived'];

        return [
            'id' => Str::orderedUuid(),
            'name' => $this->faker->company,
            'contact_person' => $this->faker->name,
            'contact_person_role' => $this->faker->jobTitle,
            'email' => $this->faker->unique()->companyEmail,
            'phone' => $this->faker->e164PhoneNumber,
            'status' => $this->faker->randomElement($statuses),
            'assigned_user' => Str::uuid(),
            'created_by' => Str::uuid(),
            'last_updated_by' => rand(0, 1) ? Str::uuid() : null,
            'is_deleted' => false,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'last_updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
