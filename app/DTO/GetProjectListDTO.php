<?php

namespace App\DTO;

class GetProjectListDTO
{
    public function __construct(
        public int $page,
        public int $perPage,
    ) {
    }
}
