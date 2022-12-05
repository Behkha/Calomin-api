<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class Search extends Controller
{
    public function __construct(Request $request)
    {
        if ($request->input('device') === 'mobile') {
            $this->middleware('auth:user');
        }
    }

    public function __invoke(Request $request)
    {
        $products = Product::where('title', 'like', '%' . $request->query('search_term') . '%')->take(5)->get();
        $brands = Brand::where('title', 'like', '%' . $request->query('search_term') . '%')->take(5)->get();
        return response()->json([
            'data' => [
                'products' => $products,
                'brands' => $brands,
            ],
        ]);
    }
}
