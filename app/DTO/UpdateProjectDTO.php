<?php

namespace App\DTO;

class UpdateProjectDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {
    }
}
