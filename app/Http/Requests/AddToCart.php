<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AddToCart extends FormRequest
{
    public function rules(Request $request)
    {
        return [
            'product_id' => ['required', 'exists:products,id', function ($attr, $value, $fail) {
                if (\Route::currentRouteName() === 'carts.add') {
                    if (auth('user')->user()->cart->products->contains('id', $value)) {
                        $fail('Product With id : ' . $value . ' Already Is In Cart');
                    }
                } elseif (\Route::currentRouteName() === 'carts.update') {
                    if (!auth('user')->user()->cart->products->contains('id', $value)) {
                        $fail('Product With id : ' . $value . ' Is Not In Cart');
                    }
                }
            }],
            'quantity' => [
                'required',
                'integer',
                'min:0',
                function ($attr, $value, $fail) use ($request) {
                    $product = Product::find($request->input('product_id'));
                    if ($product && $product->quantity < $value) {
                        $fail($attr . ' is invalid');
                    }
                },
            ],
        ];
    }
}
