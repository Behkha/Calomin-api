<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    public $timestamps = false;

    // many-to-many relation
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
