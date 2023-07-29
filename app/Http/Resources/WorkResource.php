<?php

namespace App\Http\Resources;

use App\Models\Work;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin Work
 */
#[OA\Schema(properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'date', type: 'string'),
    new OA\Property(property: 'hours', type: 'string'),
    new OA\Property(property: 'description', type: 'string'),
    new OA\Property(property: 'createdAt', type: 'string'),
    new OA\Property(property: 'updatedAt', type: 'string'),
])]
class WorkResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d'),
            'hours' => $this->hours,
            'description' => $this->description,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
