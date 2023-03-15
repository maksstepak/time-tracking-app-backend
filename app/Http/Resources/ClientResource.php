<?php

namespace App\Http\Resources;

use App\Models\Client;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin Client
 */
#[OA\Schema(properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'name', type: 'string'),
    new OA\Property(property: 'description', type: 'string', nullable: true),
    new OA\Property(property: 'contactEmail', type: 'string', nullable: true),
    new OA\Property(property: 'contactPhone', type: 'string', nullable: true),
    new OA\Property(property: 'createdAt', type: 'string'),
    new OA\Property(property: 'updatedAt', type: 'string'),
])]
class ClientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'contactEmail' => $this->contact_email,
            'contactPhone' => $this->contact_phone,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
