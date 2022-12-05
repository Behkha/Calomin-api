<?php

namespace App\Http\Requests;

use App\Models\Phone;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class VerifyPhone extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'phone' => [
                'bail',
                'required',
                'digits:11',
                function ($attr, $value, $fail) {
                    $userExists = User::where('phone', $value)
                        ->where('is_verified', true)
                        ->exists();

                    if ($userExists) {
                        return $fail($attr . ' is already verified');
                    }

                    $result = Phone::where('number', $value)
                        ->where('is_verified', false)
                        ->exists();

                    if (!$result) {
                        return $fail($attr . ' is invalid');
                    }
                },
            ],
            'token' => [
                'required',
                'digits:4',
                function ($attr, $value, $fail) use ($request) {
                    $phone = Phone::where('number', $request->input('phone'))
                        ->latest()
                        ->first();

                    if ($phone && $phone->token !== $value) {
                        $fail($attr . ' is invalid');
                    }
                },
            ],
        ];
    }
}
