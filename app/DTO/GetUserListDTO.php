<?php

namespace App\DTO;

class GetUserListDTO
{
    public function __construct(
        public int $page,
        public int $perPage,
    ) {
    }
}
