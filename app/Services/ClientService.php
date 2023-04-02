<?php

namespace App\Services;

use App\DTO\CreateClientDTO;
use App\DTO\GetClientListDTO;
use App\DTO\UpdateClientDTO;
use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function getList(GetClientListDTO $dto): LengthAwarePaginator
    {
        $builder = Client::query();
        $builder->orderByDesc('created_at')->orderByDesc('id');

        return $builder->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    public function create(CreateClientDTO $dto): Client
    {
        $client = new Client;
        $client->name = $dto->name;
        $client->description = $dto->description;
        $client->contact_email = $dto->contactEmail;
        $client->contact_phone = $dto->contactPhone;
        $client->save();

        return $client;
    }

    public function update(Client $client, UpdateClientDTO $dto): void
    {
        $client->name = $dto->name;
        $client->description = $dto->description;
        $client->contact_email = $dto->contactEmail;
        $client->contact_phone = $dto->contactPhone;
        $client->save();
    }

    public function delete(Client $client): void
    {
        DB::transaction(function () use ($client) {
            $client->projects()->delete();
            $client->delete();
        });
    }
}
