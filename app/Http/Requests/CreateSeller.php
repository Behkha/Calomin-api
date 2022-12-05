<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSeller extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'story' => 'string|max:10000',
            'profile_picture' => 'file|image|max:5000',
            'gallery' => 'array|min:1|max:10',
            'gallery.*' => 'required|file|image|max:5000',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
        ];
    }
}
