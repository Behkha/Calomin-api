<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Validation\Rule;
use App\Models\DiscountCode;

class StoreTransaction extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(Request $request)
    {
        if ($request->order->user_phone !== auth('user')->user()->phone) {

            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            
            'type' => [

                'required',

                Rule::in(Transaction::TRANSACTION_TYPES),
            ],

            'discount_code' => [

                'string',

                'size:10',

                function ($attr, $value, $fail) {

                    $result = DiscountCode::where('code', $value)
                        ->where('user_phone', auth('user')->user()->phone)
                        ->where('is_used', false)
                        ->where('expiration_date', '>=', now()->toDateString())
                        ->first();
                    
                    if (! $result) {

                        return $fail($attr . ' is invalid');
                    }

                    $request->merge(['discount_code_id' => $result->id]);
                }
            ]
        ];
    }
}
