<?php

namespace App\DTO;

class GetUserWorkListDTO
{
    public function __construct(
        public int $page,
        public int $perPage,
    ) {
    }
}
