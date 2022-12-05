<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Morilog\Jalali\Jalalian;

class Seller extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile_picture' => $this->profile_picture,
            'state' => $this->state,
            'city' => $this->city->name,
            'story' => $this->story,
            'gallery' => $this->gallery,
            'created_at' => Jalalian::forge($this->created_at)->format('%B %Y'),
            'is_following' => $this->when(auth('user')->user(), $this->isBeingFollowedBy()),
            'followers_count' => $this->followers()->count(),
            'is_rated' => $this->when(auth('user')->user(), $this->isRatedBy()),
            'rating' => $this->rating,
        ];
    }
}
