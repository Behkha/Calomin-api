<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = ['product_id', 'to', 'from', 'price', 'bonus'];
    
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
