<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\Work;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class WorkTest extends TestCase
{
    public function test_user_creates_work_in_assigned_project()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();
        $project->users()->sync([$user->id]);

        $response = $this->actingAs($user)->postJson('/api/projects/'.$project->id.'/works', [
            'date' => '2023-07-29',
            'hours' => 8,
            'description' => 'Test test test',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('works', [
            'project_id' => $project->id,
            'user_id' => $user->id,
            'date' => '2023-07-29',
            'hours' => 8,
            'description' => 'Test test test',
        ]);
    }

    public function test_user_cannot_create_work_in_unassigned_project()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Project $project */
        $project = Project::factory()->forClient()->create();

        $response = $this->actingAs($user)->postJson('/api/projects/'.$project->id.'/works', [
            'date' => '2023-07-29',
            'hours' => 8,
            'description' => 'Test test test',
        ]);

        $response->assertForbidden();
    }

    public function test_user_reads_work()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Work $work */
        $work = Work::factory()
            ->for(Project::factory()->forClient())
            ->for($user)
            ->create();

        $response = $this->actingAs($user)->getJson('/api/works/'.$work->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $work->id,
                'hours' => $work->hours,
                'date' => $work->date->format('Y-m-d'),
                'description' => $work->description,
            ]);
    }

    public function test_user_cannot_read_other_users_work()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();
        /** @var Work $work */
        $work = Work::factory()
            ->for(Project::factory()->forClient())
            ->for($anotherUser)
            ->create();

        $response = $this->actingAs($user)->getJson('/api/works/'.$work->id);

        $response->assertForbidden();
    }

    public function test_user_reads_work_list()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Collection $work */
        $works = Work::factory()
            ->for(Project::factory()->forClient())
            ->for($user)
            ->count(3)
            ->create();

        $response = $this->actingAs($user)->getJson('/api/users/'.$user->id.'/works?page=1&perPage=15');

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 3,
                'lastPage' => 1,
            ])
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment(['id' => $works->get(0)->id])
            ->assertJsonFragment(['id' => $works->get(1)->id])
            ->assertJsonFragment(['id' => $works->get(2)->id]);
    }

    public function test_user_cannot_read_other_users_work_list()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/users/'.$anotherUser->id.'/works?page=1&perPage=15');

        $response->assertForbidden();
    }

    public function test_user_updates_work()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Work $work */
        $work = Work::factory()
            ->for(Project::factory()->forClient())
            ->for($user)
            ->create();

        $response = $this->actingAs($user)->putJson('/api/works/'.$work->id, [
            'projectId' => $work->project_id,
            'date' => '2012-01-01',
            'hours' => 1,
            'description' => 'Updated',
        ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('works', [
            'id' => $work->id,
            'date' => '2012-01-01',
            'hours' => 1,
            'description' => 'Updated',
        ]);
    }

    public function test_user_cannot_update_other_users_work()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();
        /** @var Work $work */
        $work = Work::factory()
            ->for(Project::factory()->forClient())
            ->for($anotherUser)
            ->create();

        $response = $this->actingAs($user)->putJson('/api/works/'.$work->id, [
            'projectId' => $work->project_id,
            'date' => '2012-01-01',
            'hours' => 1,
            'description' => 'Updated',
        ]);

        $response->assertForbidden();
    }

    public function test_user_deletes_work()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Work $work */
        $work = Work::factory()
            ->for(Project::factory()->forClient())
            ->for($user)
            ->create();

        $response = $this->actingAs($user)->deleteJson('/api/works/'.$work->id);

        $response->assertNoContent();
        $this->assertDatabaseMissing('works', [
            'id' => $work->id,
        ]);
    }

    public function test_user_cannot_delete_other_users_work()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();
        /** @var Work $work */
        $work = Work::factory()
            ->for(Project::factory()->forClient())
            ->for($anotherUser)
            ->create();

        $response = $this->actingAs($user)->deleteJson('/api/works/'.$work->id);

        $response->assertForbidden();
    }
}
