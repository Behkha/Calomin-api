<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class SignupUser extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'digits:11',
                function ($attr, $value, $fail) {
                    $user = User::where('phone', $value)
                        ->first();

                    if ($user && $user->is_verified) {
                        $fail($attr . ' already exist');
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'max:255',
                'email',
                function ($attr, $value, $fail) {
                    $user = User::where('email', $value)
                        ->first();

                    if ($user && $user->is_verified) {
                        $fail($attr . ' already exist');
                    }
                },
            ],
            'password' => 'required|string|min:8|max:30',
        ];
    }
}
