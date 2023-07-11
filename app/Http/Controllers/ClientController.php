<?php

namespace App\Http\Controllers;

use App\DTO\CreateClientDTO;
use App\DTO\GetClientListDTO;
use App\DTO\UpdateClientDTO;
use App\Http\Requests\GetClientListRequest;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientPaginatedCollection;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientSelectOptionCollection;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ClientController
{
    public function __construct(
        private ClientService $clientService,
    ) {
    }

    #[OA\Get(path: '/api/clients', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'page', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'perPage', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ClientPaginatedCollection'))]
    public function index(GetClientListRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Client::class);
        $dto = new GetClientListDTO(...$request->validated());

        $paginator = $this->clientService->getList($dto);

        return response()->json(new ClientPaginatedCollection($paginator));
    }

    #[OA\Post(path: '/api/clients', security: [['bearerAuth' => []]])]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/StoreClientRequest'))]
    #[OA\Response(response: Response::HTTP_CREATED, description: 'OK')]
    public function store(StoreClientRequest $request): JsonResponse
    {
        Gate::authorize('create', Client::class);
        $dto = new CreateClientDTO(...$request->validated());

        $this->clientService->create($dto);

        return response()->json(null, Response::HTTP_CREATED);
    }

    #[OA\Get(path: '/api/clients/{clientId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'clientId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ClientResource'))]
    public function show(Client $client): JsonResponse
    {
        Gate::authorize('view', $client);

        return response()->json(new ClientResource($client));
    }

    #[OA\Put(path: '/api/clients/{clientId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'clientId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/UpdateClientRequest'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        Gate::authorize('update', $client);
        $dto = new UpdateClientDTO(...$request->validated());

        $this->clientService->update($client, $dto);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Delete(path: '/api/clients/{clientId}', security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'clientId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'OK')]
    public function destroy(Client $client): JsonResponse
    {
        Gate::authorize('delete', $client);

        $this->clientService->delete($client);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Get(path: '/api/clients/select-options', security: [['bearerAuth' => []]])]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ClientSelectOptionCollection'))]
    public function getSelectOptions(): JsonResponse
    {
        Gate::authorize('viewAny', Client::class);

        $selectOptions = $this->clientService->getSelectOptions();

        return response()->json(new ClientSelectOptionCollection($selectOptions));
    }
}
