<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class ForgetPassword extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'string',
                'digits:11',
                function ($attr, $value, $fail) {
                    $userExists = User::where('phone', $value)
                        ->where('is_verified', true)
                        ->exists();

                    if (! $userExists) {
                        $fail($attr . ' does not belong to an account');
                    }
                },
            ],
        ];
    }
}
