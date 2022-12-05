<?php

namespace App\Http\Resources;

use App\Models\User as UserModel;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_picture' => $this->profile_picture,
            'id_code' => $this->id_code,
            'home_phone' => $this->home_phone,
            'want_notification' => $this->want_notification,
            'birth_date' => $this->birth_date,
            'sex' => array_search($this->sex, UserModel::SEX),
            'state' => $this->state,
            'city' => $this->city,
            'wallet' => $this->wallet,
        ];
    }
}
