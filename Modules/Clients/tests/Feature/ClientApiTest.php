<?php

namespace Modules\Clients\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Clients\app\Models\Client;
use Tests\TestCase;
use Illuminate\Support\Str;

class ClientApiTest extends TestCase
{
    use RefreshDatabase; // This trait refreshes the database for each test

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('module:migrate Clients'); // Run module migrations
    }

    /** @test */
    public function it_creates_a_client()
    {
        $response = $this->postJson('/api/v1/clients', [
            'name' => 'Test Client',
            'email' => 'test@client.com',
            'phone' => '+1234567890',
            'contact_person' => 'John Doe',
            'contact_person_role' => 'CEO',
            'status' => 'active',
            'assigned_user' => Str::uuid(), // Mock UUID
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email'
                ]
            ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'test@client.com'
        ]);
    }

    /** @test */
    public function it_updates_a_client()
    {
        $client = Client::factory()->create();

        $response = $this->putJson("/api/v1/clients/{$client->id}", [
            'name' => 'Updated Name',
            'status' => 'inactive'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Name'
            ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Name'
        ]);
    }

    /** @test */
    public function it_soft_deletes_a_client()
    {
        $client = Client::factory()->create();

        $response = $this->deleteJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('clients', [
            'id' => $client->id
        ]);
    }
}
