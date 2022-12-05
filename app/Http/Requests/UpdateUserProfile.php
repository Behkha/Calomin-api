<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfile extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'id_code' => 'digits:10',
            'home_phone' => 'digits:11',
            'email' => [
                'required',
                'string', 
                'max:255', 
                'email', 
                Rule::unique('users')->ignore(auth('user')->user())
            ],
            'want_notification' => 'required|boolean',
            'birth_date' => 'date',
            'sex' => 'in:male,female',
            'state_id' => 'exists:states,id',
            'city_id' => 'required_with:state_id|exists:cities,id',
        ];
    }
}
