<?php

namespace App\DTO;

class UpdateUserDTO
{
    public function __construct(
        public string $name,
        public ?string $jobTitle,
        public bool $isAdmin,
        public ?string $password = null,
    ) {
    }
}
