<?php

namespace App\Services;

use App\DTO\CreateProjectDTO;
use App\DTO\GetProjectListDTO;
use App\DTO\UpdateProjectDTO;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;

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
}
