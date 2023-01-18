<?php

namespace App\Http\Controllers;

use App\DTO\LoginUserDTO;
use App\Exceptions\WrongCredentialsException;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController
{
    public function __construct(
        private AuthenticationService $authenticationService,
    ) {
    }

    #[OA\Post(path: '/api/login')]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/LoginUserRequest'))]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(properties: [
        new OA\Property(property: 'accessToken', type: 'string'),
    ]))]
    #[OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Wrong credentials')]
    public function login(LoginUserRequest $request): JsonResponse
    {
        $dto = new LoginUserDTO(...$request->validated());

        try {
            $accessToken = $this->authenticationService->login($dto);
        } catch (WrongCredentialsException) {
            return response()->json(['error' => 'wrongCredentials'], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['accessToken' => $accessToken]);
    }

    #[OA\Get(path: '/api/me', security: [['bearerAuth' => []]])]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/UserResource'))]
    public function getCurrentUser(Request $request): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        return response()->json(new UserResource($currentUser));
    }

    #[OA\Post(path: '/api/logout', security: [['bearerAuth' => []]])]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function logout(Request $request): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $request->user();
        $currentUser->currentAccessToken()->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
