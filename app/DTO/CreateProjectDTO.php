<?php

namespace App\DTO;

class CreateProjectDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {
    }
}
