<?php

namespace App\DTO;

class RemoveUsersFromProjectDTO
{
    public function __construct(
        public array $userIds,
    ) {
    }
}
