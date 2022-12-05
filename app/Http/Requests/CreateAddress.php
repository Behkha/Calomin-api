<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use App\Models\Address;
use Illuminate\Http\Request;

class CreateAddress extends FormRequest
{
    public function authorize(Request $request)
    {
        if (Route::currentRouteName() === 'address.update') {
            $address = Address::where('user_id', auth('user')->user()->id)
                ->find($request->route('id'));
                
            if (! $address) {
                return false;
            }
        }

        return true;
    }

    public function rules()
    {
        return [
            'receiver_name' => 'string|max:255',
            'receiver_phone' => 'string|digits:11',
            'receiver_home_phone' => 'string|digits:8',
            'receiver_home_phone_prefix' => 'required_with:receiver_home_phone|string|digits:3',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'zipcode' => 'required|string|digits:10',
            'address' => 'required|string|max:1000',
            'is_default' => 'boolean',
        ];
    }
}
