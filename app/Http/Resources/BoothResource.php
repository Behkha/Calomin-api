<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use \Morilog\Jalali\Jalalian;

class BoothResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'seller' => new Seller($this->whenLoaded('seller')),
            'logo' => $this->image,
            'category' => new Category($this->whenLoaded('category')),
            'created_at' => Jalalian::forge($this->created_at)->format('%B %Y'),
            'is_following' => $this->when(auth('user')->user(), $this->isBeingFollowedBy()),
            'rating' => $this->rating,
            'follwers_count' => $this->followers()->count(),
        ];
    }
}
