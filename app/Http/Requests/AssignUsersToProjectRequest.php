<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(properties: [
    new OA\Property(property: 'userIds', type: 'array', items: new OA\Items(type: 'integer')),
])]
class AssignUsersToProjectRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'userIds' => 'required|array',
            'userIds.*' => 'required|integer|exists:users,id',
        ];
    }
}
