<?php

namespace App\Services;

use App\DTO\LoginUserDTO;
use App\Exceptions\WrongCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    public function login(LoginUserDTO $dto): string
    {
        /** @var ?User $user */
        $user = User::query()->where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new WrongCredentialsException();
        }

        return $user->createToken('apiToken')->plainTextToken;
    }
}
