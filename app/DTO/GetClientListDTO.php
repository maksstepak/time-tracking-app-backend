<?php

namespace App\DTO;

class GetClientListDTO
{
    public function __construct(
        public int $page,
        public int $perPage,
    ) {
    }
}
