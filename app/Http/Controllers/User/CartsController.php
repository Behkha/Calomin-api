<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCart;
use App\Http\Resources\Cart;
use App\Http\Resources\Product as ProductResource;
use App\Models\Product;

class CartsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.user');
        $this->middleware('auth:user');
    }

    public function store(AddToCart $request)
    {
        $product = Product::find($request->input('product_id'));
        $cartId = auth('user')
            ->user()
            ->cart
            ->id;
        $id = \DB::table('cart_product')
            ->insertGetId([
                'cart_id' => $cartId,
                'product_id' => $product->id,
                'quantity' => $request->input('quantity'),
            ]);
        return response()->json(['Message' => 'Product Added To Cart'], 201);
    }

    // Get User Cart
    public function index()
    {
        $cartId = auth('user')
            ->user()
            ->cart
            ->id;
        $cartProds = \DB::table('cart_product')
            ->where('cart_id', $cartId)
            ->get();
        $data = ['products' => []];
        $totalPrice = 0;
        $totalDiscountPrice = 0;
        foreach ($cartProds as $cartProd) {
            $resource = new ProductResource(Product::find($cartProd->product_id));
            $totalPrice += $cartProd->quantity * $resource->discount_price;
            $totalDiscountPrice += ($cartProd->quantity * $resource->price) - ($cartProd->quantity * $resource->discount_price);
            $product = [
                'details' => $resource,
                'cart_qty' => $cartProd->quantity,
                'final_price' => $cartProd->quantity * $resource->discount_price,
                'total_discount' => ($cartProd->quantity * $resource->price) - ($cartProd->quantity * $resource->discount_price),
                'total_bonus' => $cartProd->quantity * $resource->bonus,
            ];
            array_push($data['products'], $product);
        }
        // Cart Total Price
        $data['final_price'] = $totalPrice;
        $data['total_price_without_discount'] = $totalPrice + $totalDiscountPrice;
        return response()->json(['data' => $data]);
    }

    public function update(AddToCart $request)
    {
        $cart = auth('user')
            ->user()
            ->cart;
        $product = $cart->products
            ->where('id', $request->input('product_id'))
            ->first();
        if ($request->input('quantity') == 0) {
            $product->pivot->delete();
            return response()->json(['Message' => 'Cart Updated']);
        }
        $product
            ->carts()
            ->updateExistingPivot($cart->id, ['quantity' => $request->input('quantity')]);
        return response()->json(['Message' => 'Cart Updated']);
    }

    public function destroy($id)
    {
        $cart = auth('user')
            ->user()
            ->cart;
        $product = $cart->products
            ->where('id', $id)
            ->first();
        if (!$product) {
            return response()->json(['Error' => 'Product Does Not Exists In Cart'], 400);
        }
        $cart->products()->detach($product->id);
        return response()->json(['Message' => 'Product Deleted From Cart']);
    }

    public function similarProducts()
    {
        $products = auth('user')->user()->getCart();

        $categories = $products->map(function ($value, $key) {
            return $value->category->id;
        });

        $similarProducts = Product::whereNotIn('id', $products->pluck('id')->all())
            ->whereHas('category', function ($query) use ($categories) {
                $query->whereIn('id', $categories->all());
            })
            ->take(10)
            ->get();

        return ProductResource::collection($similarProducts);
    }

    // check mikone hame attribute haye product bayad bashan
    private function validateAttributes($request)
    {
        $product = Product::find($request->input('product_id'));
        $attrs = \DB::table('attribute_product')
            ->select('attribute_id')
            ->where('product_id', $product->id)
            ->groupBy('attribute_id')
            ->get();
        $inputAttrs = [];
        foreach ($request->input('attributes') as $attr) {
            array_push($inputAttrs, (int) $attr['id']);
        }
        $prodAttrs = [];
        foreach ($attrs->all() as $attr) {
            array_push($prodAttrs, $attr->attribute_id);
        }
        if (count(array_diff($prodAttrs, $inputAttrs)) !== 0) {
            return false;
        }
        return true;
    }

    // check mikone value ha bashan
    private function validateValues($request)
    {
        $product = Product::find($request->input('product_id'));
        foreach ($request->input('attributes') as $attr) {
            $exists = \DB::table('attribute_product')
                ->where('product_id', $product->id)
                ->where('attribute_id', $attr['id'])
                ->where('value', $attr['value'])
                ->exists();
            if (!$exists) {
                return false;
            }
        }
        return true;
    }

    // check mikone too cart nabashe
    private function validateExists($request)
    {
        $product = Product::find($request->input('product_id'));
        $cartId = auth('user')
            ->user()
            ->cart
            ->id;
        $exists = \DB::table('cart_product')
            ->where('cart_id', $cartId)
            ->where('product_id', $product->id)
            ->exists();
        if (!$exists) {
            return true;
        }
        foreach ($request->input('attributes') as $attr) {
            $exists = \DB::table('cart_product_attributes')
                ->where('product_id', $product->id)
                ->where('attribute_id', $attr['id'])
                ->where('value', $attr['value'])
                ->exists();
            if (!$exists) {
                return true;
            }
        }
        return false;
    }

    // Item Tooye Cart Ro Mide
    private function getItem($request)
    {
        $first = \DB::table('cart_product_attributes')
            ->join('cart_product', 'cart_product.id', '=', 'cart_product_attributes.cart_product_id')
            ->where('cart_product_attributes.product_id', $request->input('product_id'))
            ->where('cart_product_attributes.attribute_id', $request->input('attributes')[0]['id'])
            ->where('cart_product_attributes.value', $request->input('attributes')[0]['value'])
            ->where('cart_product.product_id', $request->input('product_id'))
            ->where('cart_product.cart_id', auth('user')->user()->cart->id)
            ->get();
        for ($i = 1; $i < count($request->input('attributes')); $i++) {
            $temp = \DB::table('cart_product_attributes')
                ->join('cart_product', 'cart_product.id', '=', 'cart_product_attributes.cart_product_id')
                ->where('cart_product_attributes.product_id', $request->input('product_id'))
                ->where('cart_product_attributes.attribute_id', $request->input('attributes')[$i]['id'])
                ->where('cart_product_attributes.value', $request->input('attributes')[$i]['value'])
                ->where('cart_product.product_id', $request->input('product_id'))
                ->where('cart_product.cart_id', auth('user')->user()->cart->id)
                ->get();
            $first = $first->all();
            foreach ($first as $key => $arr) {
                $sw = false;
                foreach ($temp as $arr2) {
                    if ($arr->cart_product_id == $arr2->cart_product_id) {
                        $sw = true;
                        break;
                    }
                }
                if (!$sw) {
                    unset($first[$key]);
                }
            }
        }
        return array_pop($first);
    }
}
