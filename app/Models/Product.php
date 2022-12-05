<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'gallery' => 'json',
        'has_tax' => 'boolean',
        'mass' => 'integer',
    ];

    protected $hidden = ['purchased_price'];

    protected $fillable = [
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
        'created_at',
        'updated_at',
        'mass',
    ];

    public $timestamps = false;

    public function getImagesAttribute()
    {
        if ($this->gallery) {
            $images = [];
            foreach ($this->gallery as $image) {
                if (strstr($image, 'http://lorempixel.com')) {
                    array_push($images, $image);
                } else {
                    array_push($images, env('STORAGE_PATH') . $image);
                }
            }
            return $images;
        } else {
            return null;
        }

    }

    public function getDiscountPriceAttribute()
    {
        if ($this->discounts->count() !== 3) {
            return null;
        }
        foreach ($this->discounts as $discount) {
            if ($this->counter_sales === 0 || ($this->counter_sales >= $discount->from && $this->counter_sales <= $discount->to)) {
                return $discount->price;
            }
            if ($this->counter_sales > $this->discounts[2]->to) {
                return $this->discounts[2]->price;
            }
        }
        return null;
    }

    public function getDiscountBonusAttribute()
    {
        if ($this->discounts->count() !== 3) {
            return null;
        }
        foreach ($this->discounts as $discount) {
            if ($this->counter_sales == 0) {
                return $discount->bonus;
            }
            if ($this->counter_sales >= $discount->from && $this->counter_sales <= $discount->to) {
                return $discount->bonus;
            }
            if ($this->counter_sales > $this->discounts[2]->to) {
                return $this->discounts[2]->bonus;
            }
        }
        return null;
    }

    public function getFinalPriceAttribute()
    {
        return 1;
    }

    public function getRemainingTimeAttribute()
    {
        $timer = new Carbon($this->counter_created_at);
        return $timer->diffInSeconds(now());
    }

    public function getTaxAttribute()
    {
        return floor(($this->discount_price * 9) / 100);
    }

    public function attributes()
    {
        return $this
            ->belongsToMany(Attribute::class)
            ->withPivot('value');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function discounts()
    {
        return $this
            ->hasMany(Discount::class)
            ->orderBy('from', 'asc');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot(
            'quantity', 'price', 'price_before_discount'
        );
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function carts()
    {
        return $this
            ->belongsToMany(Cart::class)
            ->withPivot('quantity');
    }

    public function isBookmarked()
    {
        $user = auth('user')->user();
        if (!$user) {
            return false;
        }
        return $this->bookmarks->contains('user_id', $user->id);
    }

    public function getCartQuantity()
    {
        if (!auth('user')->user()) {
            return false;
        }
        return \DB::table('cart_product')
            ->where('cart_id', auth('user')->user()->cart->id)
            ->where('product_id', $this->id)
            ->count('quantity');
    }

    // return count of this product in orders with status unknown
    public function getUnknownOrderCount()
    {
        $orders = Order::where('status', Order::STATUS['unknown'])
            ->where('created_at', '>=', now()->subMinutes(60)->toDateTimeString())
            ->whereHas('products', function ($query) {
                $query->where('id', $this->id);
            })
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            foreach ($order->products as $product) {
                if ($product->id == $this->id) {
                    $count += $product->pivot->quantity;

                    break;
                }
            }
        }

        return $count;
    }
}
