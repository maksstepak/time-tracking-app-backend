<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class ClientTest extends TestCase
{
    public function test_admin_creates_client()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/clients', [
            'name' => 'Client',
            'description' => null,
            'contactEmail' => null,
            'contactPhone' => null,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('clients', [
            'name' => 'Client',
        ]);
    }

    public function test_user_cannot_create_client()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/clients', [
            'name' => 'Client',
            'description' => null,
            'contactEmail' => null,
            'contactPhone' => null,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_reads_client()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        $response = $this->actingAs($admin)->getJson('/api/clients/'.$client->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $client->id,
                'name' => $client->name,
            ]);
    }

    public function test_user_cannot_read_client()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/clients/'.$client->id);

        $response->assertForbidden();
    }

    public function test_admin_reads_client_list()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Collection $clients */
        $clients = Client::factory()->count(3)->create();

        $response = $this->actingAs($admin)->getJson('/api/clients?page=1&perPage=15');

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 3,
                'lastPage' => 1,
            ])
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment(['name' => $clients->get(0)->name])
            ->assertJsonFragment(['name' => $clients->get(1)->name])
            ->assertJsonFragment(['name' => $clients->get(2)->name]);
    }

    public function test_user_cannot_read_client_list()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/clients?page=1&perPage=15');

        $response->assertForbidden();
    }

    public function test_admin_updates_client()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        $response = $this->actingAs($admin)->putJson('/api/clients/'.$client->id, [
            'name' => 'Updated',
            'description' => $client->description,
            'contactEmail' => $client->contact_email,
            'contactPhone' => $client->contact_phone,
        ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated',
        ]);
    }

    public function test_user_cannot_update_client()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/clients/'.$client->id, [
            'name' => 'Updated',
            'description' => $client->description,
            'contactEmail' => $client->contact_email,
            'contactPhone' => $client->contact_phone,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_deletes_client()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        $response = $this->actingAs($admin)->deleteJson('/api/clients/'.$client->id);

        $response->assertNoContent();
        $this->assertSoftDeleted($client);
    }

    public function test_user_cannot_delete_client()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/api/clients/'.$client->id);

        $response->assertForbidden();
    }
}
