<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Morilog\Jalali\Jalalian;

class Order extends Model
{
    protected $fillable = [
        'address_id',
        'delivery_method_id',
        'status',
        'code',
        'user_id',
        'price',
        'payment_method',
    ];

    public const STATUS = [
        'unknown' => 1,
        'registered' => 2,
        'verified' => 3,
        'rejected' => 4,
        'package' => 5,
        'sent' => 6,
        'delivered' => 7,
        'canceled' => 8,
        'unsuccessful' => 9,
    ];

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case 1:
                return 'نامعلوم';
            case 2:
                return 'ثبت شده';
            case 3:
                return 'تایید شده';
            case 4:
                return 'رد شده';
            case 5:
                return 'در حال بسته بندی';
            case 6:
                return 'ارسال شده';
            case 7:
                return 'تحویل داده شده';
            case 8:
                return 'لغو شده';
            case 9:
                return 'ناموفق';
        }
    }

    public function getStatusCodeAttribute()
    {
        return $this->getOriginal('status');
    }

    public function getCreatedAtAttribute($value)
    {
        return Jalalian::forge($value)->format('%Y-%m-%d %H:i');
    }

    public function getTotalPriceAttribute()
    {
        $totalPrice = 0;
        foreach ($this->products as $product) {
            $totalPrice += $product->pivot->price * $product->pivot->quantity;
        }
        $totalPrice += $this->deliveryMethod->price;
        return $totalPrice;
    }

    public function getPriceBeforeDiscountAttribute()
    {
        $price = 0;
        foreach ($this->products as $product) {
            $price += $product->pivot->price_before_discount * $product->pivot->quantity;
        }
        return $price;
    }

    public function getPriceAfterDiscountAttribute()
    {
        $price = 0;
        foreach ($this->products as $product) {
            $price += $product->pivot->price * $product->pivot->quantity;
        }
        return $price;
    }

    public function getTotalBonusAttribute()
    {
        $price = 0;
        foreach ($this->products as $product) {
            $price += $product->bonus * $product->pivot->quantity;
            $price += $product->discount_bonus;
        }
        return $price;
    }

    public function getTotalMassAttribute()
    {
        $totalMass = 0;
        foreach ($this->products as $product) {
            $totalMass += $product->mass * $product->pivot->quantity;
        }
        return $totalMass;
    }

    public function getTotalTaxAttribute()
    {
        $totalTax = 0;
        foreach ($this->products as $product) {
            if ($product->has_tax) {
                $totalTax += $product->tax * $product->pivot->quantity;
            }
        }
        return $totalTax;
    }

    public function products()
    {
        return $this
            ->belongsToMany(Product::class)
            ->withPivot('quantity', 'price', 'price_before_discount', 'bonus');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
