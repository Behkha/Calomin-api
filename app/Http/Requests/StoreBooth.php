<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBooth extends FormRequest
{
    public function rules()
    {
        return [           
            'title' => 'required|string|max:255',
            'description' => 'string|max:10000',
            'seller_id' => 'required|exists:sellers,id',
            'logo' => 'file|image|max:5000',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
