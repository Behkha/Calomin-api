<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannersIndex;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannersController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:admin')->only([
            'create',
            'delete',
            'deleteCover',
            'updateCover'
        ]);
    }

    public function index(Request $request)
    {
        $banners = Banner::all();
        if ($request->get("index"))
            $banners = $banners->where('index', $request->get('index'));
        return response()->json(['data' => $banners]);
    }

    public function show(Banner $banner)
    {
        return $banner;
    }

    public function create(Request $request)
    {
        $this->validateCreateBannerRequest($request);
        if ($error = $this->checkIndex($request))
            return response()->json($error);

        $cover = $request->file('cover')->store('banners');
        $banner = Banner::create([
            'cover' => $cover,
            'link' => $request->get('link'),
            'index' => $request->get('index')
        ]);
        $banner->save();

        return response()->json([
            'message' => 'banner created successfully'
        ]);
    }


    public function update(Banner $banner, Request $request)
    {
        $this->validateUpdateBannerRequest($request);
        if ($error = $this->checkIndex($request))
            return response()->json($error);

        $banner->update([
            'link' => $request->get('link'),
            'index' => $request->get('index')
        ]);

        return response()->json([
            'banner' => $banner,
            'message' => 'banner updated successfully'
        ]);
    }

    private function validateCreateBannerRequest(Request $request)
    {
        $request->validate([
            'cover' => 'required|file|image|max:5000',
            'link'  => 'required|string|min:1',
            'index' => 'required|integer'
        ]);
    }

    private function validateUpdateBannerRequest(Request $request)
    {
        $request->validate([
            'link'  => 'required|string|min:1',
            'index' => 'required|integer'
        ]);
    }


    public function delete(Banner $banner)
    {
        $cover = $banner->getOriginal('cover');
        Storage::delete($cover);
        $banner->delete();
        return response()->json([
            'banner' => $banner,
            'message' => 'banner deleted successfully'
        ]);
    }

    public function deleteCover(Banner $banner)
    {
        if ($banner->cover)
        {
            Storage::delete($banner->getOriginal('cover'));
            $banner->cover = null;
            $banner->save();
            return response()->json(['message'=>'banner cover deleted successfully']);
        }
        return response()->json(['message'=>'no cover found'], 404);
    }

    public function updateCover(Banner $banner, Request $request)
    {
        $request->validate(['cover' => 'required|file|image|max:5000']);
        if ($banner->cover)
        {
            Storage::delete($banner->getOriginal('cover'));
        }
        $cover = $request->file('cover')->store('banners');
        $banner->update([
            'cover' => $cover
        ]);
        return response()->json([
           'banner' => $banner,
           'message' => 'banner cover updated successfully'
        ]);
    }

    private function checkIndex(Request $request)
    {
        $banners = Banner::where('index', $request->get('index'))->get();
        $banners_index = BannersIndex::where('index', $request->get('index'))->firstOrFail();
        if (count($banners) == $banners_index->max)
            return ['message' => 'can not create more than ' . $banners_index->max . ' banner in this index',
                    'message_fa' => 'حداکثر ' . $banners_index->max . ' بنر در این محل میتوانید بسازید'
                    ];
        return null;
    }
}
