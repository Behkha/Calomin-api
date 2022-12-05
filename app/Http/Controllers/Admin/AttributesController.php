<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        if ($request->query('name')) {
            $attrs = Attribute::where(
                'name', 'like', '%' . $request->query('name') . '%'
            )->paginate();
            return response()->json(['data' => $attrs]);
        }
        $attrs = Attribute::all();
        return response()->json(['data' => $attrs]);
    }

    public function create(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'unit' => 'string|max:255']);
        $attr = Attribute::create($request->only(['name', 'unit']));
        return response()->json(['data' => $attr], 201);
    }

    public function update(Attribute $attribute, Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'unit' => 'string|max:255']);
        $attribute->update($request->only(['name', 'unit']));
        return response()->json(['data' => $attribute]);
    }

    public function delete(Attribute $attribute)
    {
        $attribute->delete();
        return response()->json(['data' => $attribute]);
    }
}
