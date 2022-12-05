<?php

namespace App\Http\Requests;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrder extends FormRequest
{
    public function rules()
    {
        return [
            'address_id' => [
                'required',
                function ($attr, $value, $fail) {
                    $exists = Address::where('user_id', auth('user')->user()->id)
                        ->find($value);

                    if (!$exists) {
                        $fail($attr . ' is invalid');
                    }
                },
            ],
            'discount_code' => 'string|max:255',
            'delivery_method_id' => 'required|exists:delivery_methods,id',
            'payment_method' => 'required|exists:payment_methods,name',
        ];
    }
}
