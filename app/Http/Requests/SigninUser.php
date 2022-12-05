<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SigninUser extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ];
    }
}
