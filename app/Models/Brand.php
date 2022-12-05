<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public $timestamps = false;

    protected $fillable = ['title', 'image_url'];

    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return env('STORAGE_PATH') . $value;
    }
}
