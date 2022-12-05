<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBooth;
use App\Http\Resources\BoothResource;
use App\Http\Resources\Product as ProductResource;
use App\Models\Booth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoothsController extends Controller
{
    public function __construct(Request $request)
    {
        if ($request->header('device') === 'mobile') {
            $this->middleware('auth:user');
        }

        $this->middleware('auth:admin')->only([
            'create',
            'update',
            'updateLogo',
        ]);
    }

    public function index(Request $request)
    {
        if ($request->query('has_discount')) {
            $booths = Booth::with('seller')->whereHas('products.discounts')->paginate();
            return BoothResource::collection($booths);
        }
        return BoothResource::collection(Booth::with('seller')->paginate());
    }

    public function show(Booth $booth)
    {
        $booth->load('category', 'seller');

        return new BoothResource($booth);
    }

    public function products(Request $request, Booth $booth)
    {
        if ($request->query('all')) {
            return ProductResource::collection($booth->products);
        }

        $products = $booth->products()->inRandomOrder()->paginate();

        return ProductResource::collection($products);
    }

    public function create(StoreBooth $request)
    {
        $booth = new Booth($request->only(['title', 'description', 'seller_id', 'category_id']));

        if ($request->hasFile('logo')) {
            $booth->logo = $request->file('logo')->store('booths');
        }

        $booth->save();

        return new BoothResource($booth);
    }

    public function update(Booth $booth, StoreBooth $request)
    {
        $booth->update($request->only(['title', 'description', 'seller_id', 'category_id']));

        if ($request->input('delete_logo') && $booth->logo) {
            Storage::delete($booth->logo);

            $booth->logo = null;

            $booth->save();
        }

        return new BoothResource($booth);
    }

    public function updateLogo(Booth $booth, Request $request)
    {
        $request->validate(['logo' => 'required|image|max:5000']);

        if ($booth->logo) {
            Storage::delete($booth->logo);
        }

        $booth->logo = $request->file('logo')->store('booths');

        $booth->save();

        return new BoothResource($booth);
    }
}
