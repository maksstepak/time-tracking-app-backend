<?php

namespace App\DTO;

class LoginUserDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}
