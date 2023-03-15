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
            'name' => 'required|string',
            'description' => 'present|nullable|string',
            'contactEmail' => 'present|nullable|string',
            'contactPhone' => 'present|nullable|string',
        ];
    }
}
