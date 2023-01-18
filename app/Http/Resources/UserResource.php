<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin User
 */
#[OA\Schema(properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'name', type: 'string'),
    new OA\Property(property: 'email', type: 'string'),
    new OA\Property(property: 'createdAt', type: 'string'),
    new OA\Property(property: 'updatedAt', type: 'string'),
])]
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
