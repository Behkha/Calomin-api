<?php

namespace App\Http\Requests;

use App\Models\PasswordReset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ChangePassword extends FormRequest
{
    public function authorize(Request $request)
    {
        $canChangePassword = PasswordReset::where('phone', $request->input('phone'))
            ->where('is_verified', true)
//            ->where('created_at', '>=', now()->subMinutes(60)->toDateTimeString())
            ->where('is_used', false)
            ->exists();

        if ($canChangePassword) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'password' => 'required|string|min:8|max:30',
        ];
    }
}
