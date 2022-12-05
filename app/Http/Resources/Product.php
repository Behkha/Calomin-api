<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class Product extends JsonResource
{
    public function toArray($request)
    {
        if (Route::currentRouteName() === 'carts.show') {
            $this->load('attributes');
        }
        $features = \DB::table('product_features')
            ->where('product_id', $this->id)
            ->get();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'gallery' => $this->images,
            'code' => $this->code,
            'has_tax' => $this->has_tax,
            'price' => $this->price,
            'tax' => $this->has_tax,
            'mass' => $this->mass,
            'order_price' => $this->whenPivotLoaded('order_product', function () {
                return $this->pivot->price;
            }),
            'order_qty' => $this->whenPivotLoaded('order_product', function () {
                return $this->pivot->quantity;
            }),
            'order_price_before_discount' =>
            $this->whenPivotLoaded('order_product', function () {
                return $this->pivot->price_before_discount;
            }),
            'order_bonus' => $this->whenPivotLoaded('order_product', function () {
                return $this->pivot->bonus;
            }),
            'quantity' => $this->quantity,
            'discount' => $this->when($this->discounts->count() === 3, $this->discounts),
            'price_after_discount' => $this->when($this->discount_price, $this->discount_price),
            // Discount Price
            'discount_price' => $this->when($this->discount_price, $this->price - $this->discount_price),
            'discount_bonus' => $this->when($this->discount_bonus, $this->discount_bonus),
            // Hedye Kharide Har Yedoone Product
            'bonus' => $this->bonus,
            // Bonus * Tedade Kharid
            // 'total_bonus' => $this->
            'attributes' => AttributeResource::collection($this->whenLoaded('attributes')),
            'description' => $this->description,
            'is_bookmarked' => $this->when(auth('user')->user(), $this->isBookmarked()),
            'category' => new Category($this->category),
            'features' => $features->pluck('title'),
            'remaining_time' => $this->remaining_time,
            'purchased_price' => $this->purchased_price,
            'brand' => $this->brand,
            'counter' => $this->counter,
        ];
    }
}
