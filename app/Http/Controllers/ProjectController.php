<?php

namespace App\Http\Controllers;

use App\DTO\AssignUsersToProjectDTO;
use App\DTO\CreateProjectDTO;
use App\DTO\GetProjectListDTO;
use App\DTO\RemoveUsersFromProjectDTO;
use App\DTO\UpdateProjectDTO;
use App\Exceptions\UserAlreadyAssignedToProjectException;
use App\Exceptions\UserNotAssignedToProjectException;
use App\Http\Requests\AssignUsersToProjectRequest;
use App\Http\Requests\GetProjectListRequest;
use App\Http\Requests\RemoveUsersFromProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectPaginatedCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Client;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ProjectController
{
    public function __construct(
        private ProjectService $projectService,
    ) {
    }

    #[OA\Get(path: '/api/projects', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'page', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'perPage', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ProjectPaginatedCollection'))]
    public function index(GetProjectListRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Project::class);
        $dto = new GetProjectListDTO(...$request->validated());

        $paginator = $this->projectService->getList($dto);

        return response()->json(new ProjectPaginatedCollection($paginator));
    }

    #[OA\Post(path: '/api/clients/{clientId}/projects', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'clientId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/StoreProjectRequest'))]
    #[OA\Response(response: Response::HTTP_CREATED, description: 'OK')]
    public function store(Client $client, StoreProjectRequest $request): JsonResponse
    {
        Gate::authorize('create', Project::class);
        $dto = new CreateProjectDTO(...$request->validated());

        $this->projectService->create($client, $dto);

        return response()->json(null, Response::HTTP_CREATED);
    }

    #[OA\Get(path: '/api/projects/{projectId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'projectId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ProjectResource'))]
    public function show(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        $project->load(['client']);

        return response()->json(new ProjectResource($project));
    }

    #[OA\Put(path: '/api/projects/{projectId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'projectId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/UpdateProjectRequest'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('update', $project);
        $dto = new UpdateProjectDTO(...$request->validated());

        $this->projectService->update($project, $dto);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Delete(path: '/api/projects/{projectId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'projectId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function destroy(Project $project): JsonResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Post(path: '/api/projects/{projectId}/assign-users', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'projectId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/AssignUsersToProjectRequest'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function assignUsers(Project $project, AssignUsersToProjectRequest $request): JsonResponse
    {
        Gate::authorize('assignUsers', $project);
        $dto = new AssignUsersToProjectDTO(...$request->validated());

        try {
            $this->projectService->assignUsers($project, $dto);
        } catch (UserAlreadyAssignedToProjectException) {
            return response()->json(['error' => 'userAlreadyAssignedToProject'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Post(path: '/api/projects/{projectId}/remove-users', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'projectId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/RemoveUsersFromProjectRequest'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function removeUsers(Project $project, RemoveUsersFromProjectRequest $request): JsonResponse
    {
        Gate::authorize('removeUsers', $project);
        $dto = new RemoveUsersFromProjectDTO(...$request->validated());

        try {
            $this->projectService->removeUsers($project, $dto);
        } catch (UserNotAssignedToProjectException) {
            return response()->json(['error' => 'userNotAssignedToProject'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
