<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(properties: [
    new OA\Property(property: 'projectId', type: 'integer'),
    new OA\Property(property: 'date', type: 'string'),
    new OA\Property(property: 'hours', type: 'float'),
    new OA\Property(property: 'description', type: 'string'),
])]
class UpdateWorkRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'projectId' => 'required|integer|exists:projects,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|between:0,24',
            'description' => 'required|string|max:10000',
        ];
    }
}
