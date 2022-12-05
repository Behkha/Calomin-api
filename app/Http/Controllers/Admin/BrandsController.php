<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PHLAK\Twine;

class BrandsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $brands = Brand::all();
        return response()->json(['data' => $brands]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'file|image|max:5000',
        ]);
        $brand = Brand::create(['title' => $request->input('title')]);
        if ($request->hasFile('image')) {
            $fileName = (new Twine\Str($brand->id . $brand->title))->base64() . '.' . $request->file('image')->guessExtension();
            $url = $request->file('image')->storeAs('brands', $fileName);
        }
        $brand->save();
        return response()->json(['data' => $brand], 201);
    }

    public function update(Brand $brand, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'file|image|max:5000',
        ]);
        $brand->title = $request->input('title');
        if ($request->input('delete_image')) {
            Storage::delete($brand->getOriginal('image_url'));
            $brand->image_url = null;
        }
        if ($request->file('image')) {
            Storage::delete($brand->getOriginal('image_url'));
            $fileName = (new Twine\Str($brand->id . $brand->title))->base64() . '.' . $request->file('image')->guessExtension();
            $brand->image_url = $request->file('image')->storeAs('brands', $fileName);
        }
        $brand->save();
        return response()->json(['data' => $brand]);
    }
}
