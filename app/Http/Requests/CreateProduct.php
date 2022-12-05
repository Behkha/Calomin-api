<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProduct extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'gallery' => 'array|max:10',
            'gallery.*' => 'required|file|image|max:5000',
            'price' => 'required|integer|min:1',
            'purchased_price' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:0',
            'description' => 'string|max:10000',
            'attributes' => 'array',
            'attributes.*.id' => 'required|distinct|exists:attributes,id',
            'attributes.*.value' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'counter' => 'integer|min:1',
            'features' => 'array',
            'features.*' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'code' => 'required|string',
            'has_tax' => 'boolean',
            'mass' => 'integer|min:0',
        ];
    }
}
