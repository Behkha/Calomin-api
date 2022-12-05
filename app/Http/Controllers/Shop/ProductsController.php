<?php

namespace App\Http\Controllers\Shop;

use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProduct;
use App\Http\Requests\UpdateProduct;
use App\Http\Resources\CommentResource;
use App\Http\Resources\Product as ProductResource;
use App\Imports\ProductCreateImport;
use App\Imports\ProductImport;
use App\Jobs\AddToRedis;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PHLAK\Twine;

class ProductsController extends Controller
{
    public function __construct(Request $request)
    {
        if ($request->header('device') === 'mobile') {
            $this->middleware('auth:user');
        }
        $this->middleware('auth:user')->only([
            'addComment',
        ]);
        $this
            ->middleware('auth:admin')
            ->only([
                'create',
                'update',
                'updateGallery',
                'deleteImage',
                'massUpdate',
                'massCreate',
                'getExcel',
            ]);
    }

    public function index(Request $request)
    {
        if ($request->query('title')) {
            $products = Product::where(
                'title', 'like', '%' . $request->query('title') . '%'
            )->paginate();
            return ProductResource::collection($products);
        }
        $request->validate([
            'categories' => 'array|min:1',
            'categories.*' => 'required|distinct|exists:categories,id',
            'min_price' => 'integer|min:1',
            'max_price' => 'integer|min:1',
            'is_available' => 'boolean',
            'has_discount' => 'boolean',
            'filters' => 'array|min:1',
            'filters.*.id' => 'required|distinct|exists:attributes,id',
            'filters.*.values' => 'required|array|min:1',
            'filters.*.values.*' => 'required|string|max:255',
        ]);
        $query = Product::query();
        // applying dynamic filters
        if ($request->query('filters')) {
            foreach ($request->query('filters') as $filter) {
                $query->orWhere(function ($builder) use ($filter) {
                    $builder->whereHas('attributes', function ($attribute) use ($filter) {
                        $attribute->where(function ($builder2) use ($filter) {
                            $builder2->where('id', $filter['id'])
                                ->whereIn('value', $filter['values']);
                        });
                    });
                });
            }
        }
        if ($request->query('categories')) {
            foreach ($request->query('categories') as $category) {
                $query->whereHas('category', function ($builder) use ($category) {
                    $builder->where('id', $category);
                });
            }
        }

        if ($request->query('min_price')) {
            $query->where('price', '>=', $request->query('min_price'));
        }

        if ($request->query('max_price')) {
            $query->where('price', '<=', $request->query('max_price'));
        }

        if ($request->query('is_available')) {
            $query->where('quantity', '>', 0);
        }

        if ($request->query('has_discount')) {
            $query->whereHas('discounts');
        }

        if ($request->query('sort_by_views')) {
            $query->orderBy('view_count', 'desc');
        }

        if ($request->query('sort_by_bookmarks')) {
            $query->withCount('bookmarks')->orderBy('bookmarks_count', 'desc');
        }

        if ($request->query('sort_by_sales')) {
            $query->withCount('orders')->orderBy('orders_count', 'desc');
        }

        if ($request->query('sort_by_lowest_price')) {
            $products = $query->get();
            foreach ($products as $product) {
                if ($product->discount_price) {
                    $product->price_after_discount = $product->discount_price;
                } else {
                    $product->price_after_discount = $product->price;
                }
            }
            $sorted = $products->sortBy('price_after_discount');
            $data = $sorted->paginate(15);
            return ProductResource::collection($data);
        } elseif ($request->query('sort_by_highest_price')) {
            $products = $query->get();
            foreach ($products as $product) {
                if ($product->discount_price) {
                    $product->price_after_discount = $product->discount_price;
                } else {
                    $product->price_after_discount = $product->price;
                }
            }
            $sorted = $products->sortByDesc('price_after_discount');
            $data = $sorted->paginate(15);
            return ProductResource::collection($data);
        }

        $products = $query->paginate();

        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        $product->view_count++;
        $product->save();
        $product->load('attributes');
        return new ProductResource($product);
    }

    public function similarProducts(Product $product)
    {
        $products = Product::whereHas('category', function ($query) use ($product) {
            $query->where('id', $product->category->id);
        })->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->paginate();
        return ProductResource::collection($products);
    }

    public function newProducts()
    {
        $products = Product::orderBy('created_at', 'desc')
            ->take(20)
            ->get();
        return ProductResource::collection($products);
    }

    public function create(CreateProduct $request)
    {
        $result = $this->calculateDiscounts($request);
        $request->merge([
            'counter_sales' => 0,
            'bonus' => $result['bonus'],
            'counter_created_at' => now()->addHours($request->input('counter')),
        ]);
        $product = Product::create($request->only([
            'title',
            'price',
            'quantity',
            'description',
            'category_id',
            'counter_sales',
            'counter',
            'bonus',
            'purchased_price',
            'counter_created_at',
            'brand_id',
            'code',
            'has_tax',
            'mass',
        ]));
        $gallery = [];
        $attrs = [];
        if ($request->file('gallery')) {
            foreach ($request->file('gallery') as $key => $image) {
                $fileName = (new Twine\Str($product->id . $product->title))->base64() . $key . '.' . $image->guessExtension();
                array_push($gallery, $image->storeAs('products', $fileName));
            }
        }
        if ($request->input('attributes')) {
            foreach ($request->input('attributes') as $attribute) {
                array_push($attrs, ['attribute_id' => $attribute['id'], 'value' => $attribute['value']]);
            }
        }
        $product->gallery = $gallery;
        $product->attributes()->attach($attrs);
        $product->save();
        // save features
        if ($request->input('features')) {
            foreach ($request->input('features') as $feature) {
                \DB::table('product_features')
                    ->insert(['title' => $feature, 'product_id' => $product->id]);
            }
        }
        $this->createDiscounts($result, $request->input('quantity'), $product->id, $request->input('price'));
        return new ProductResource($product);
    }

    public function update(Product $product, UpdateProduct $request)
    {
        $result = $this->calculateDiscounts($request);
        $product->update($request->only([
            'title',
            'price',
            'quantity',
            'description',
            'category_id',
            'bonus',
            'counter',
            'brand_id',
            'purchased_price',
            'code',
            'has_tax',
            'mass',
        ]));
        $attrs = [];
        // delete gallery
        if ($request->input('delete_gallery')) {
            if ($product->gallery) {
                foreach ($product->gallery as $image) {
                    Storage::delete($image);
                }
                $product->gallery = null;
                $product->save();
            }
        }

        if ($request->input('attributes')) {
            foreach ($request->input('attributes') as $attribute) {
                array_push($attrs, ['attribute_id' => $attribute['id'], 'value' => $attribute['value']]);
            }
        }
        $product->attributes()->detach();
        $product->attributes()->attach($attrs);
        // save features
        if (sizeof($request->input('features')) > 0) {
            \DB::table('product_features')
                ->where('product_id', $product->id)
                ->delete();
            foreach ($request->input('features') as $feature) {
                \DB::table('product_features')
                    ->insert(['title' => $feature, 'product_id' => $product->id]);
            }
        }
        Discount::where('product_id', $product->id)->delete();
        $this->createDiscounts($result, $request->input('quantity'), $product->id, $request->input('price'));
        return new ProductResource($product);
    }

    public function updateGallery(Product $product, Request $request)
    {
        $request->validate([
            'gallery' => 'required|array|min:1|max:10',
            'gallery.*' => 'required|file|image|max:5000',
        ]);
        $temp = $product->gallery;
        $gallery = [];
        foreach ($request->gallery as $image) {
            $fileName = (new Twine\Str($product->id . $product->title))->base64() . $key . '.' . $image->guessExtension();
            array_push($gallery, $image->storeAs('products', $fileName));
        }
        $product->gallery = $temp;
        $product->save();
        return new ProductResource($product);
    }

    public function addComment(Product $product, Request $request)
    {
        $request->validate(['body' => 'required|string|max:255']);
        $product->comments()->save(new Comment([
            'body' => $request->input('body'),
            'commented_by' => auth('user')->user()->id,
        ]));
        return response()->json(['message' => 'comment added'], 201);
    }

    public function getComments(Product $product)
    {
        $comments = $product->comments()->paginate();
        return CommentResource::collection($comments);
    }

    public function massUpdate(Request $request)
    {
        $request->validate(['excel' => 'required|file|max:5000']);
        if ($request->file('excel')->getClientOriginalExtension() !== 'xlsx') {
            return response()->json(['errors' => 'invalid file format'], 400);
        }
        Excel::import(new ProductImport, $request->file('excel'));
        return response()->json(['message' => 'ok']);
    }

    public function massCreate(Request $request)
    {
        $request->validate(['excel' => 'required|file|max:5000']);
        if ($request->file('excel')->getClientOriginalExtension() !== 'xlsx') {
            return response()->json(['errors' => 'invalid file format'], 400);
        }
        Excel::import(new ProductCreateImport, $request->file('excel'));
        return response()->json(['message' => 'ok']);
    }

    public function getExcel()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    private function storeGallery($request, &$product)
    {
        if ($request->hasFile('gallery')) {

            $images = [];

            foreach ($request->file('gallery') as $image) {

                array_push($images, $image->store('products'));
            }

            $product->gallery = $images;
        }
    }

    private function storeAttributes($request, &$product)
    {
        if ($request->input('attributes')) {

            $attrs = [];

            foreach ($request->input('attributes') as $attribute) {

                array_push($attrs, [
                    'attribute_name' => $attribute['name'],
                    'value' => $attribute['value'],
                ]);
            }

            $product->attributes()->attach($attrs);
        }
    }

    private function storeFeatures($request, &$product)
    {
        if ($request->input('features')) {

            $product->features()->attach($request->input('features'));
        }
    }

    private function validateIndexProducts($request)
    {
        $request->validate([
            'paginate' => 'integer|min:1|max:20',

            'page' => 'integer|min:1',

            'sort_by' => 'in:view,bookmark,price',

            'sort_order' => 'in:asc,desc',
        ]);
    }

    private function getProducts($key, $start, $end)
    {
        if (($key === 'products_by_price' && request()->input('sort_order') === 'desc') || $key === 'products_by_bookmark') {

            $ids = Redis::ZREVRANGE($key, $start, $end);
        } else {

            $ids = Redis::ZRANGE($key, $start, $end);
        }

        $products = collect();

        foreach ($ids as $id) {

            $product = unserialize(Redis::GET('product:' . $id));

            if (!$product) {

                $product = Product::find($id);

                AddToRedis::dispatch('Product', $product->id, $product);
            }

            $products->push($product);
        }

        return ProductResource::collection($products);
    }

    public function getFilters(Request $request)
    {
        $request->validate(['category_id' => 'exists:categories,id']);
        if ($request->query('category_id')) {
            $attributes = collect();
            $category = Category::find($request->query('category_id'));
            $category->attributes->each(function ($value, $key) use ($attributes) {
                $attributes->push($value);
            });
            foreach (Category::where('parent_id', $category->id)->get() as $child) {
                foreach (Category::where('parent_id', $child->id)->get() as $second) {
                    foreach (Category::where('parent_id', $second->id)->get() as $third) {
                        $third->attributes->each(function ($value, $key) use ($attributes) {
                            $attributes->push($value);
                        });
                    }
                    $second->attributes->each(function ($value, $key) use ($attributes) {
                        $attributes->push($value);
                    });
                }
                $child->attributes->each(function ($value, $key) use ($attributes) {
                    $attributes->push($value);
                });
            }
            $result = [];
            foreach ($attributes->unique('id') as $item) {
                $values = \DB::table('attribute_product')
                    ->where('attribute_id', $item->id)
                    ->select('value')
                    ->groupBy('value')
                    ->get();
                if ($values->count() > 0) {
                    array_push($result, [
                        'key_id' => $item->id,
                        'key_name' => $item->name,
                        'values' => $values,
                    ]);
                }
            }
            return $result;
        }
        $result = [];
        $attrs = Attribute::all();
        foreach ($attrs as $attr) {
            $values = \DB::table('attribute_product')
                ->where('attribute_id', $attr->id)
                ->select('value')
                ->groupBy('value')
                ->get();
            array_push($result, ['key_id' => $attr->id, 'key_name' => $attr->name, 'values' => $values]);

        }
        return response()->json(['data' => $result]);
    }

    public function deleteImage(Product $product, $imageUrl)
    {
        if (sizeof($product->gallery) === 0) {
            return response()->json(['error' => 'does not have gallery'], 400);
        }
        foreach ($product->gallery as $key => $image) {
            if (explode('/', $image)[1] === $imageUrl) {
                $gallery = $product->gallery;
                unset($gallery[$key]);
                Storage::delete($image);
                $product->gallery = $gallery;
                $product->save();
                return response()->json(['message' => 'image deleted']);
            }
        }
        return response()->json(['error' => 'image not found'], 404);
    }

    private function calculateDiscounts($request)
    {
        $discount = floor(($request->input('price') - $request->input('purchased_price')) / 6);
        $first = $request->input('price') - $discount;
        $second = $first - $discount;
        $third = $second - $discount;
        $bonus = floor(floor(($request->input('price') - $third) / 10) / 2);
        $fbonus = $bonus;
        $sbonus = floor($fbonus / 2);
        $tbonus = floor($sbonus / 2);
        return [
            'firstDiscount' => $first,
            'secondDiscount' => $second,
            'thirdDiscount' => $third,
            'bonus' => $bonus,
            'firstBonus' => $fbonus,
            'secondBonus' => $sbonus,
            'thirdBonus' => $tbonus,
        ];
    }

    private function createDiscounts($discounts, $qty, $productId, $price)
    {
        if ($price < 100000) {
            Discount::create([
                'product_id' => $productId,
                'from' => 1,
                'to' => 24,
                'price' => $discounts['firstDiscount'],
                'bonus' => $discounts['firstBonus'],
            ]);
            Discount::create([
                'product_id' => $productId,
                'from' => 25,
                'to' => 50,
                'price' => $discounts['secondDiscount'],
                'bonus' => $discounts['secondBonus'],
            ]);
            Discount::create([
                'product_id' => $productId,
                'from' => 51,
                'to' => 100,
                'price' => $discounts['thirdDiscount'],
                'bonus' => $discounts['thirdBonus'],
            ]);
        } elseif ($price >= 100000 && $price <= 300000) {
            Discount::create([
                'product_id' => $productId,
                'from' => 1,
                'to' => 5,
                'price' => $discounts['firstDiscount'],
                'bonus' => $discounts['firstBonus'],
            ]);
            Discount::create([
                'product_id' => $productId,
                'from' => 6,
                'to' => 25,
                'price' => $discounts['secondDiscount'],
                'bonus' => $discounts['secondBonus'],
            ]);
            Discount::create([
                'product_id' => $productId,
                'from' => 26,
                'to' => 100,
                'price' => $discounts['thirdDiscount'],
                'bonus' => $discounts['thirdBonus'],
            ]);
        } else {
            Discount::create([
                'product_id' => $productId,
                'from' => 1,
                'to' => 2,
                'price' => $discounts['firstDiscount'],
                'bonus' => $discounts['firstBonus'],
            ]);
            Discount::create([
                'product_id' => $productId,
                'from' => 3,
                'to' => 5,
                'price' => $discounts['secondDiscount'],
                'bonus' => $discounts['secondBonus'],
            ]);
            Discount::create([
                'product_id' => $productId,
                'from' => 6,
                'to' => 10,
                'price' => $discounts['thirdDiscount'],
                'bonus' => $discounts['thirdBonus'],
            ]);
        }
    }
}
