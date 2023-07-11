<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Schema(type: 'array', items: new OA\Items(ref: '#/components/schemas/ClientSelectOptionResource'))]
class ClientSelectOptionCollection extends ResourceCollection
{
    public $collects = ClientSelectOptionResource::class;

    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
