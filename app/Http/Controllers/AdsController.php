<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Http\Requests\StoreAd;

class AdsController extends Controller
{
    public function __construct(Request $request)
    {
        if ($request->input('device') === 'mobile') {
            $this->middleware('auth:user');
        }

        $this->middleware('auth:admin')
            ->only([
                'store',
            ]);
    }

    public function index()
    {
        $ads = Ad::paginate();
        
        return response()->json(['data' => $ads]);
    }

    public function show(Ad $ad)
    {
        return response()->json(['data' => $ad]);
    }

    public function store(StoreAd $request)
    {
        $ad = new Ad($request->all());

        $ad->image = $request->file('image')->store('ads');

        $ad->save();

        return response()->json(['data' => $ad], 201);
    }
}
