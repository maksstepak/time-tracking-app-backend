<?php

namespace App\DTO;

class UpdateWorkDTO
{
    public function __construct(
        public int $projectId,
        public string $date,
        public float $hours,
        public string $description,
    ) {
    }
}
