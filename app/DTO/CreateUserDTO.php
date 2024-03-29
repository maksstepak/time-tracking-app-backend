<?php

namespace App\DTO;

class CreateUserDTO
{
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
        public ?string $jobTitle = null,
        public bool $isAdmin = false,
    ) {
    }
}
