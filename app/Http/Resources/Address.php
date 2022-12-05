<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Address extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'receiver_name' => $this->receiver_name,
            'receiver_phone' => $this->receiver_phone,
            'receiver_home_phone' => $this->receiver_home_phone,
            'receiver_home_phone_prefix' => $this->receiver_home_phone_prefix,
            'state' => $this->state,
            'city' => $this->city,
            'zipcode' => $this->zipcode,
            'address' => $this->address,
            'is_default' => $this->is_default,
        ];
    }
}
