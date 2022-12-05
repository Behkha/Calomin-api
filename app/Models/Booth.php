<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Booth extends Model
{
    // use Searchable;

    protected $fillable = [
        'title',
        'description',
        'seller_id',
        'category_id',
    ];

    public function getImageAttribute()
    {
        if (strpos($this->logo, 'http://lorempixel.com/400/200/') === 0) {
            return $this->logo;
        }

        return $this->logo ? env('STORAGE_PATH') . $this->logo : null;
    }

    public function getRatingAttribute()
    {
        $score = $this->rates->sum('score');

        $total = $this->rates->count();

        return $total !== 0 ? $score / $total : 0;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'booth_followers', 'booth_id', 'followed_by');
    }

    public function isBeingFollowedBy()
    {
        $user = auth('user')->user();

        if (!$user) {
            return false;
        }
        
        return $this->followers()->where('followed_by', $user->id)->exists();
    }

    public function ratedByUser()
    {
        return $this->rates()->where('rated_by', auth('user')->user()->id)->exists();
    }

    //check if current user is followign this booth
    public function isFollowing()
    {
        if (auth('user')->user()) {
            return Redis::SISMEMBER('booth:' . $this->id . 'followers', auth('user')->user()->id);
        }

        return false;
    }

    // public function toSearchableArray()
    // {
    //     $array = $this->toArray();

    //     $newArray = [];

    //     $newArray['title'] = isset($array['title']) ? $array['title'] : null;

    //     $newArray['description'] = isset($array['description']) ? $array['description'] : null;

    //     return $newArray;
    // }
}
