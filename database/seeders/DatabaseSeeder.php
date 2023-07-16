<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(3)->admin()->create();
        /** @var Collection $users */
        $users = User::factory(10)->create();

        Client::factory(15)->hasProjects(3)->create()->each(function (Client $client) use ($users) {
            $client->projects->each(function (Project $project) use ($users) {
                $usersToAssign = $users->random(3);
                $project->users()->sync($usersToAssign->pluck('id')->toArray());
            });
        });
    }
}
