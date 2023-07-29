<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Attributes as OA;

/**
 * @mixin LengthAwarePaginator
 */
#[OA\Schema(properties: [
    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/WorkResource')),
    new OA\Property(property: 'total', type: 'integer'),
    new OA\Property(property: 'lastPage', type: 'integer'),
])]
class WorkPaginatedCollection extends ResourceCollection
{
    public $collects = WorkResource::class;

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'total' => $this->total(),
            'lastPage' => $this->lastPage(),
        ];
    }
}
