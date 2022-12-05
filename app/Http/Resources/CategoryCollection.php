<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\Pagination;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => Category::collection($this->collection),
        ];
    }

    public function with($request)
    {
        return [
            'meta' => Pagination::meta($request),
        ];
    }
}
