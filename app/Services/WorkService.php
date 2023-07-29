<?php

namespace App\Services;

use App\DTO\CreateWorkDTO;
use App\DTO\GetUserWorkListDTO;
use App\DTO\UpdateWorkDTO;
use App\Exceptions\UserNotAssignedToProjectException;
use App\Models\Project;
use App\Models\User;
use App\Models\Work;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class WorkService
{
    public function getUserWorkList(User $user, GetUserWorkListDTO $dto): LengthAwarePaginator
    {
        $builder = Work::query();
        $builder
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        return $builder->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    public function create(Project $project, CreateWorkDTO $dto): Work
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $work = new Work;
        $work->date = $dto->date;
        $work->hours = $dto->hours;
        $work->description = $dto->description;
        $work->user_id = $currentUser->id;

        $project->works()->save($work);

        return $work;
    }

    public function update(Work $work, UpdateWorkDTO $dto): void
    {
        /** @var Project $project */
        $project = Project::query()->find($dto->projectId);
        $isUserAssignedToProject = $project->users()->where('users.id', $work->user_id)->exists();
        if (!$isUserAssignedToProject) {
            throw new UserNotAssignedToProjectException();
        }

        $work->project_id = $dto->projectId;
        $work->date = $dto->date;
        $work->hours = $dto->hours;
        $work->description = $dto->description;
        $work->save();
    }
}
