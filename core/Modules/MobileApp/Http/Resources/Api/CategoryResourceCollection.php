<?php

namespace Modules\MobileApp\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @see \Modules\Attributes\Entities\Category */

class CategoryResourceCollection extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $request->id,
            'name' => $request->name,
            'image' => render_image($request->image,render_type: 'path'),
        ];
    }
}
