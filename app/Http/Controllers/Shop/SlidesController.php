<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slide;
use Illuminate\Support\Facades\Storage;

class SlidesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->only([
            'create',
            'delete'
        ]);
    }

    public function index()
    {
        $slides = Slide::all();
        return response()->json(['data' => $slides]);
    }

    public function create(Request $request)
    {
        $request->validate(['image' => 'required|file|image|max:5000']);
        $slideImageUrl = $request->file('image')->store('slides');
        $slide = Slide::create(['image_url' => $slideImageUrl]);
        return response()->json(['data' => $slide], 201);
    }

    public function delete(Slide $slide)
    {
        $imageUrl = $slide->getOriginal('image_url');
        Storage::delete($imageUrl);
        $slide->delete();
        return response()->json(['data' => $slide]);
    }
}
