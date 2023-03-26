<?php

namespace App\Console\Commands;

use App\DTO\CreateUserDTO;
use App\Services\UserService;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'create-admin';

    protected $description = 'Create admin';

    public function handle(UserService $userService)
    {
        $email = $this->ask('Email');
        $name = $this->ask('Name');
        $password = $this->secret('Password');

        $dto = new CreateUserDTO($email, $name, $password, isAdmin: true);
        $userService->create($dto);

        $this->info('Admin has been created');
    }
}
