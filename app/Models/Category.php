<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function getImageAttribute()
    {
        return $this->logo ? env('STORAGE_PATH') . $this->logo : null;
    }

    public function getLogoAttribute($value)
    {
        return $value ? env('STORAGE_PATH') . $value : null;
    }

    public function isBookmarkedByUser()
    {
        $user = auth('user')->user();

        return Redis::SISMEMBER('user:' . $user->id . ':bookmarks', 'category:' . $this->id);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class);
    }
}
