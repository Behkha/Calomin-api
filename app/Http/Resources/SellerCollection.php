<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\Pagination;

class SellerCollection extends ResourceCollection
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
            'data' => Seller::collection($this->collection),
        ];
    }

    public function with($request)
    {
        return [
            'links' => Pagination::links($request),
            'meta' => Pagination::meta($request),
        ];
    }
}
