<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategory extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'logo' => 'file|image|max:5000',
            'parent_id' => 'exists:categories,id',
            'attributes' => 'array|min:1',
            'attributes.*' => 'required|exists:attributes,id',
        ];
    }
}
