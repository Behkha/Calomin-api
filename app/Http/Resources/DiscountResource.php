<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'discount_percent' => $this->discount_percent,
            'expiration_date' => $this->expiration_date,
        ];
    }
}
