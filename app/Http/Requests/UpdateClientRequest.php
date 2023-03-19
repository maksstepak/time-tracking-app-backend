<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(properties: [
    new OA\Property(property: 'name', type: 'string'),
    new OA\Property(property: 'description', type: 'string', nullable: true),
    new OA\Property(property: 'contactEmail', type: 'string', nullable: true),
    new OA\Property(property: 'contactPhone', type: 'string', nullable: true),
])]
class UpdateClientRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:64',
            'description' => 'present|nullable|string|max:10000',
            'contactEmail' => 'present|nullable|string|max:255',
            'contactPhone' => 'present|nullable|string|max:255',
        ];
    }
}
