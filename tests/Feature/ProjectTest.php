<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    public function test_admin_creates_project()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        $response = $this->actingAs($admin)->postJson('/api/clients/'.$client->id.'/projects', [
            'name' => 'Project',
            'description' => null,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('projects', [
            'client_id' => $client->id,
            'name' => 'Project',
            'description' => null,
        ]);
    }

    public function test_user_cannot_create_project()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/clients/'.$client->id.'/projects', [
            'name' => 'Project',
            'description' => null,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_reads_project()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();

        $response = $this->actingAs($admin)->getJson('/api/projects/'.$project->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $project->id,
                'name' => $project->name,
            ]);
    }

    public function test_user_cannot_read_project()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();

        $response = $this->actingAs($user)->getJson('/api/projects/'.$project->id);

        $response->assertForbidden();
    }

    public function test_admin_reads_project_list()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Collection $projects */
        $projects = Project::factory()->forClient()->count(3)->create();

        $response = $this->actingAs($admin)->getJson('/api/projects?page=1&perPage=15');

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 3,
                'lastPage' => 1,
            ])
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment(['name' => $projects->get(0)->name])
            ->assertJsonFragment(['name' => $projects->get(1)->name])
            ->assertJsonFragment(['name' => $projects->get(2)->name]);
    }

    public function test_user_cannot_read_project_list()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/projects?page=1&perPage=15');

        $response->assertForbidden();
    }

    public function test_admin_updates_project()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();

        $response = $this->actingAs($admin)->putJson('/api/projects/'.$project->id, [
            'name' => 'Updated',
            'description' => $project->description,
        ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated',
        ]);
    }

    public function test_user_cannot_update_project()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();

        $response = $this->actingAs($user)->putJson('/api/projects/'.$project->id, [
            'name' => 'Updated',
            'description' => $project->description,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_deletes_project()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();

        $response = $this->actingAs($admin)->deleteJson('/api/projects/'.$project->id);

        $response->assertNoContent();
        $this->assertSoftDeleted($project);
    }

    public function test_user_cannot_delete_project()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();

        $response = $this->actingAs($user)->deleteJson('/api/projects/'.$project->id);

        $response->assertForbidden();
    }

    public function test_admin_assigns_users_to_project()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();
        /** @var Collection $usersToAssign */
        $usersToAssign = User::factory(3)->create();

        $response = $this->actingAs($admin)->postJson('/api/projects/'.$project->id.'/assign-users', [
            'userIds' => $usersToAssign->pluck('id')->toArray(),
        ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $usersToAssign->get(0)->id,
        ]);
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $usersToAssign->get(1)->id,
        ]);
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $usersToAssign->get(2)->id,
        ]);
    }

    public function test_admin_remove_users_from_project()
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();
        /** @var Collection $assignedUsers */
        $assignedUsers = User::factory(3)->create();
        $project->users()->sync($assignedUsers->pluck('id')->toArray());

        $response = $this->actingAs($admin)->postJson('/api/projects/'.$project->id.'/remove-users', [
            'userIds' => $assignedUsers->pluck('id')->toArray(),
        ]);

        $response->assertNoContent();
        $this->assertDatabaseMissing('project_user', [
            'project_id' => $project->id,
            'user_id' => $assignedUsers->get(0)->id,
        ]);
        $this->assertDatabaseMissing('project_user', [
            'project_id' => $project->id,
            'user_id' => $assignedUsers->get(1)->id,
        ]);
        $this->assertDatabaseMissing('project_user', [
            'project_id' => $project->id,
            'user_id' => $assignedUsers->get(2)->id,
        ]);
    }
}
