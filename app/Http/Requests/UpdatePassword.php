<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class UpdatePassword extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $user = auth('user')->user();

        $oldPassword = $request->input('old_password');

        if (! Hash::check($oldPassword, $user->password)) {
            throw ValidationException::withMessages([
                'old_password' => 'password is invalid',
            ]);
        }

        return [
            'old_password' => 'required|string',
            'new_password' => 'required|string|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
        ];
    }
}
