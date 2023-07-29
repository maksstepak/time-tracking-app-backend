<?php

namespace App\DTO;

class CreateWorkDTO
{
    public function __construct(
        public string $date,
        public float $hours,
        public string $description,
    ) {
    }
}
