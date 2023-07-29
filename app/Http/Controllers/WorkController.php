<?php

namespace App\Http\Controllers;

use App\DTO\CreateWorkDTO;
use App\DTO\GetUserWorkListDTO;
use App\DTO\UpdateWorkDTO;
use App\Exceptions\UserNotAssignedToProjectException;
use App\Http\Requests\GetUserWorkListRequest;
use App\Http\Requests\StoreWorkRequest;
use App\Http\Requests\UpdateWorkRequest;
use App\Http\Resources\WorkPaginatedCollection;
use App\Http\Resources\WorkResource;
use App\Models\Project;
use App\Models\User;
use App\Models\Work;
use App\Services\WorkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class WorkController
{
    public function __construct(
        private WorkService $workService,
    ) {
    }

    #[OA\Get(path: '/api/users/{userId}/works', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'page', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'perPage', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/WorkPaginatedCollection'))]
    public function getUserWorkList(User $user, GetUserWorkListRequest $request): JsonResponse
    {
        Gate::authorize('getUserWorkList', [Work::class, $user]);
        $dto = new GetUserWorkListDTO(...$request->validated());

        $paginator = $this->workService->getUserWorkList($user, $dto);

        return response()->json(new WorkPaginatedCollection($paginator));
    }

    #[OA\Post(path: '/api/projects/{projectId}/works', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'projectId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/StoreWorkRequest'))]
    #[OA\Response(response: Response::HTTP_CREATED, description: 'OK')]
    public function store(StoreWorkRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('create', [Work::class, $project]);
        $dto = new CreateWorkDTO(...$request->validated());

        $this->workService->create($project, $dto);

        return response()->json(null, Response::HTTP_CREATED);
    }

    #[OA\Get(path: '/api/works/{workId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'workId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/WorkResource'))]
    public function show(Work $work): JsonResponse
    {
        Gate::authorize('view', $work);

        return response()->json(new WorkResource($work));
    }

    #[OA\Put(path: '/api/works/{workId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'workId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/UpdateWorkRequest'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function update(UpdateWorkRequest $request, Work $work): JsonResponse
    {
        Gate::authorize('update', $work);
        $dto = new UpdateWorkDTO(...$request->validated());

        try {
            $this->workService->update($work, $dto);
        } catch (UserNotAssignedToProjectException) {
            return response()->json(['error' => 'userNotAssignedToProject'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Delete(path: '/api/works/{workId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'workId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function destroy(Work $work): JsonResponse
    {
        Gate::authorize('delete', $work);

        $work->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
