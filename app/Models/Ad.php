<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'link',
    ];

    public $timestamps = false;

    public function getImageAttribute($image)
    {
        return env('STORAGE_PATH') . $image;
    }
}
