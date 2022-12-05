<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Redis;

class ProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return Product::collection($this->collection);
    }

    public function with($request)
    {
        return [
            'meta' => [
                'count' => Redis::ZCARD('products_by_bookmark'),
                'next' => '',
                'prev' => '',
            ],
        ];
    }
}
