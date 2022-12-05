<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class DiscountCode extends Model
{
    public $timestamps = false;

    protected $casts = [
        'is_used' => 'boolean',
    ];

    public function calculateDiscount($orderPrice)
    {
        $off = floor(($orderPrice * $this->percent)) / 100;

        if ($off > $this->max) {
            return $orderPrice - $this->max;
        }

        return floor(($orderPrice (100 - $this->percent)) / 100);
    }

    public function getExpirationDateAttribute($value)
    {
        return Jalalian::forge(new Carbon($value))->format('%Y-%m-%d');
    }
}
