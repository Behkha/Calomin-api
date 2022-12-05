<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Brand;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return response()->json(['data' => $brands]);
    }

    public function show(Brand $brand)
    {
        return response()->json(['data' => $brand]);
    }
}
