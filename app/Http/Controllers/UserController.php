<?php

namespace App\Http\Controllers;

use App\DTO\CreateUserDTO;
use App\DTO\GetUserListDTO;
use App\DTO\UpdateUserDTO;
use App\Http\Requests\GetUserListRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserPaginatedCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    public function __construct(
        private UserService $userService,
    ) {
    }

    #[OA\Get(path: '/api/users', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'page', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'perPage', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/UserPaginatedCollection'))]
    public function index(GetUserListRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', User::class);
        $dto = new GetUserListDTO(...$request->validated());

        $paginator = $this->userService->getList($dto);

        return response()->json((new UserPaginatedCollection($paginator)));
    }

    #[OA\Post(path: '/api/users', security: [['bearerAuth' => []]])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/StoreUserRequest'))]
    #[OA\Response(response: Response::HTTP_CREATED, description: 'OK')]
    public function store(StoreUserRequest $request): JsonResponse
    {
        Gate::authorize('create', User::class);
        $dto = new CreateUserDTO(...$request->validated());

        $this->userService->create($dto);

        return response()->json(null, Response::HTTP_CREATED);
    }

    #[OA\Get(path: '/api/users/{userId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/UserResource'))]
    public function show(User $user): JsonResponse
    {
        Gate::authorize('view', $user);

        return response()->json(new UserResource($user));
    }

    #[OA\Put(path: '/api/users/{userId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/UpdateUserRequest'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        Gate::authorize('update', $user);
        $dto = new UpdateUserDTO(...$request->validated());

        $this->userService->update($user, $dto);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Delete(path: '/api/users/{userId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
