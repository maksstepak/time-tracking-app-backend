<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Models\Work;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Work $work)
    {
        return $user->id === $work->user_id;
    }

    public function create(User $user, Project $project)
    {
        return $project->users()->where('users.id', $user->id)->exists();
    }

    public function update(User $user, Work $work)
    {
        return $user->id === $work->user_id;
    }

    public function delete(User $user, Work $work)
    {
        return $user->id === $work->user_id;
    }

    public function getUserWorkList(User $user, User $model)
    {
        return $user->id === $model->id;
    }
}
