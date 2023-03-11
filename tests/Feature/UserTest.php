<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_admin_creates_user()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/users', [
            'email' => 'test@test.com',
            'name' => 'Test',
            'password' => 'password',
            'jobTitle' => 'Job title',
            'isAdmin' => false,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
            'name' => 'Test',
            'job_title' => 'Job title',
            'is_admin' => false,
        ]);
    }

    public function test_user_cannot_create_user()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/users', [
            'email' => 'test@test.com',
            'name' => 'Test',
            'password' => 'password',
            'jobTitle' => 'Job title',
            'isAdmin' => false,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_reads_user()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->getJson('/api/users/'.$user->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_user_reads_himself()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/users/'.$user->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_user_cannot_read_another_user()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/users/'.$anotherUser->id);

        $response->assertForbidden();
    }

    public function test_admin_reads_user_list()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Collection $users */
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin)->getJson('/api/users?page=1&perPage=15');
        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 4,
                'lastPage' => 1,
            ])
            ->assertJsonCount(4, 'data')
            ->assertJsonFragment(['email' => $users->get(0)->email])
            ->assertJsonFragment(['email' => $users->get(1)->email])
            ->assertJsonFragment(['email' => $users->get(2)->email]);
    }

    public function test_user_cannot_read_user_list()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/users?page=1&perPage=15');

        $response->assertForbidden();
    }

    public function test_admin_updates_user()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->putJson('/api/users/'.$user->id, [
            'name' => 'Updated',
            'password' => null,
            'jobTitle' => $user->job_title,
            'isAdmin' => $user->is_admin,
        ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated',
        ]);
    }

    public function test_user_cannot_update_user()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $userToUpdate */
        $userToUpdate = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/users/'.$userToUpdate->id, [
            'name' => 'Updated',
            'password' => null,
            'jobTitle' => $userToUpdate->job_title,
            'isAdmin' => $userToUpdate->is_admin,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_deletes_user()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->deleteJson('/api/users/'.$user->id);

        $response->assertNoContent();
        $this->assertSoftDeleted($user);
    }

    public function test_user_cannot_delete_user()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $userToDelete */
        $userToDelete = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/api/users/'.$userToDelete->id);

        $response->assertForbidden();
    }
}
