<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

#[OA\Schema(properties: [
    new OA\Property(property: 'name', type: 'string', nullable: true),
    new OA\Property(property: 'password', type: 'string'),
    new OA\Property(property: 'jobTitle', type: 'string', nullable: true),
    new OA\Property(property: 'isAdmin', type: 'boolean'),
])]
class UpdateUserRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:64',
            'password' => ['present', 'nullable', Password::min(8)],
            'jobTitle' => 'present|nullable|string|max:64',
            'isAdmin' => 'required|boolean',
        ];
    }
}
