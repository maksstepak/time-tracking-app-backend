<?php

namespace App\DTO;

class AssignUsersToProjectDTO
{
    public function __construct(
        public array $userIds,
    ) {
    }
}
