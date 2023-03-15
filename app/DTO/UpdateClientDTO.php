<?php

namespace App\DTO;

class UpdateClientDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $contactEmail = null,
        public ?string $contactPhone = null,
    ) {
    }
}
