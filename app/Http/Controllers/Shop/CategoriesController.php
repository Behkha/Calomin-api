<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategory;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHLAK\Twine;

class CategoriesController extends Controller
{
    public function __construct(Request $request)
    {
        if ($request->input('device') === 'mobile') {
            $this->middleware('auth:user');
        }
        $this->middleware('auth:admin')->only(['create', 'update']);
    }

    public function index(Request $request)
    {
        $this->validateIndex($request);
        if ($request->query('name')) {
            $categories = Category::where(
                'name', 'like', '%' . $request->query('name') . '%'
            )->paginate();
            return CategoryResource::collection($categories);
        }
        $query = Category::orderBy('id', 'asc');
        if ($request->input('all')) {
            $categories = Category::where('parent_id', null)
                ->get();
            if ($categories->count() === 0) {
                return response()->json(['data' => '']);
            }
            foreach ($categories as $category) {
                $category->children = collect();
                foreach (Category::where('parent_id', $category->id)->get() as $child) {
                    $fchildren = [];
                    foreach (Category::where('parent_id', $child->id)->get() as $second) {
                        $schildren = [];
                        foreach (Category::where('parent_id', $second->id)->get() as $third) {
                            array_push($schildren, $third);
                        }
                        $second->children = $schildren;
                        array_push($fchildren, $second);
                    }
                    $child->children = $fchildren;
                    $category->children->push($child);
                }
            }
            if (!$category->children) {
                $category->children = [];
            }
            return $categories;
        }
        return CategoryResource::collection($query->paginate());
    }

    public function show(Category $category)
    {
        $category->load('attributes');
        $category->parent = Category::find($category->parent_id);
        $category->children = collect();
        foreach (Category::where('parent_id', $category->id)->get() as $child) {
            $fchildren = [];
            foreach (Category::where('parent_id', $child->id)->get() as $second) {
                $schildren = [];
                foreach (Category::where('parent_id', $second->id)->get() as $third) {
                    $third->load('attributes');
                    array_push($schildren, $third);
                }
                $second->load('attributes');
                $second->children = $schildren;
                array_push($fchildren, $second);
            }
            $child->load('attributes');
            $child->children = $fchildren;
            $category->children->push($child);
        }
        return $category;
    }

    public function products(Category $category, Request $request)
    {
        $products = $category->products()->paginate();
        if ($request->query('with_children')) {
            $products = collect();
            $temp = Category::where('parent_id', $category->id)
                ->get();
            $products->push(Product::collection($category->products));
            while ($temp->count() !== 0) {
                $element = $temp->pop();
                $tempChild = Category::find($element->id);
                if ($tempChild->products->count() > 0) {
                    $products->push(Product::collection($tempChild->products));
                }
                Category::where('parent_id', $tempChild->id)
                    ->get()
                    ->each(function ($item, $key) use ($temp) {
                        $temp->push($item);
                    });
            }
            return response()->json(['data' => $products]);
        }
        return Product::collection($products);
    }

    public function create(StoreCategory $request)
    {
        $category = Category::create($request->only(['name']));
        if ($request->hasFile('logo')) {
            $fileName = (new Twine\Str($category->id . $category->name))->base64() . '.' . $request->file('logo')->guessExtension();
            $category->logo = $request->file('logo')->storeAs('categories', $fileName);
        }
        if ($request->input('parent_id')) {
            $category->parent_id = $request->input('parent_id');
        }
        $category->save();
        if ($request->input('attributes')) {
            $category->attributes()->attach($request->input('attributes'));
        }
        return new CategoryResource($category);
    }

    public function update(Category $category, StoreCategory $request)
    {
        if ($request->input('delete_logo') && $request->hasFile('logo')) {
            return response()->json(['errors' => 'delete_logo and logo can not be present at the same time'], 400);
        }
        if ($request->input('delete_logo')) {
            Storage::delete($category->getOriginal('logo'));
            $category->logo = null;
        }
        if ($request->hasFile('logo')) {
            if ($category->logo) {
                Storage::delete($category->getOriginal('logo'));
            }
            $fileName = (new Twine\Str($category->id . $category->name))->base64() . '.' . $request->file('logo')->guessExtension();
            $category->logo = $request->file('logo')->storeAs('categories', $fileName);
        }
        DB::transaction(function () use ($request, &$category) {
            $category->name = $request->input('name');
            $category->parent_id = $request->input('parent_id');
            $category->save();
            if ($request->input('attributes')) {
                $category->attributes()->detach();
                $category->attributes()->attach($request->input('attributes'));
            }
        });
        return new CategoryResource($category);
    }

    public function attributes(Category $category)
    {
        $attributes = collect();
        $attributes->push($category->attributes);
        foreach (Category::where('parent_id', $category->id)->get() as $child) {
            foreach (Category::where('parent_id', $child->id)->get() as $second) {
                foreach (Category::where('parent_id', $second->id)->get() as $third) {
                    $attributes->push($third->attributes);
                }
                $attributes->push($second->attributes);
            }
            $attributes->push($child->attributes);
        }
        $data = collect();
        foreach ($attributes as $attribute) {
            foreach ($attribute as $result) {
                $data->push($result);
            }
        }
        return AttributeResource::collection($data);
    }

    /*
     * ------------------------------------------------------------------
     * Secondary Methods
     * ------------------------------------------------------------------
     */

    private function validateIndex($request)
    {
        $request->validate([
            'page' => 'integer|min:1',
            'all' => 'boolean',
        ]);
    }
}
