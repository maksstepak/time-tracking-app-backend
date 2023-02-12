<?php

namespace App\Services;

use App\DTO\CreateUserDTO;
use App\DTO\GetUserListDTO;
use App\DTO\UpdateUserDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getList(GetUserListDTO $dto): LengthAwarePaginator
    {
        $builder = User::query();
        $builder->orderByDesc('created_at')->orderByDesc('id');

        return $builder->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    public function create(CreateUserDTO $dto): User
    {
        $user = new User;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->name = $dto->name;
        $user->is_admin = $dto->isAdmin;
        $user->job_title = $dto->jobTitle;
        $user->save();

        return $user;
    }

    public function update(User $user, UpdateUserDTO $dto): void
    {
        $user->name = $dto->name;
        $user->job_title = $dto->jobTitle;
        $user->is_admin = $dto->isAdmin;
        if (! empty($dto->password)) {
            $user->password = Hash::make($dto->password);
        }
        $user->save();
    }
}
