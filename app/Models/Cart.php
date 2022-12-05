<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id'];
    
    public $timestamps = false;

    public function products()
    {
        return $this
            ->belongsToMany(Product::class)
            ->withPivot('quantity');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
