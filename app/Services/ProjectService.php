<?php

namespace App\Services;

use App\DTO\AssignUsersToProjectDTO;
use App\DTO\CreateProjectDTO;
use App\DTO\GetProjectListDTO;
use App\DTO\RemoveUsersFromProjectDTO;
use App\DTO\UpdateProjectDTO;
use App\Exceptions\UserAlreadyAssignedToProjectException;
use App\Exceptions\UserNotAssignedToProjectException;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function getList(GetProjectListDTO $dto): LengthAwarePaginator
    {
        $builder = Project::query();
        $builder
            ->with(['client'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        return $builder->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    public function create(Client $client, CreateProjectDTO $dto): Project
    {
        $project = new Project;
        $project->name = $dto->name;
        $project->description = $dto->description;
        $client->projects()->save($project);

        return $project;
    }

    public function update(Project $project, UpdateProjectDTO $dto): void
    {
        $project->name = $dto->name;
        $project->description = $dto->description;
        $project->save();
    }

    public function assignUsers(Project $project, AssignUsersToProjectDTO $dto)
    {
        $isUserAlreadyAssigned = $project->users()->whereIn('users.id', $dto->userIds)->exists();
        if ($isUserAlreadyAssigned) {
            throw new UserAlreadyAssignedToProjectException();
        }

        DB::transaction(function () use ($dto, $project) {
            $project->users()->syncWithoutDetaching($dto->userIds);
        });
    }

    public function removeUsers(Project $project, RemoveUsersFromProjectDTO $dto)
    {
        $assignedUsersCount = $project->users()->whereIn('users.id', $dto->userIds)->count();
        if ($assignedUsersCount !== count($dto->userIds)) {
            throw new UserNotAssignedToProjectException();
        }

        DB::transaction(function () use ($dto, $project) {
            $project->users()->detach($dto->userIds);
        });
    }
}
