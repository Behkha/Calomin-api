<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $casts = [
        'gallery' => 'json',
    ];

    protected $fillable = [
        'name',
        'story',
        'state_id',
        'city_id',
    ];

    public function getProfilePictureAttribute($value)
    {
        if (strpos($value, 'http://lorempixel.com/400/200/') === 0) {
            return $value;
        }

        return $value ? env('STORAGE_PATH') . $value : null;
    }

    public function getGalleryAttribute($value)
    {
        $images = [];

        foreach (json_decode($value) as $image) {
            if (strpos($image, 'http://lorempixel.com/400/200/') === 0) {
                array_push($images, $image);
            } else {
                array_push($images, env('STORAGE_PATH') . $image);
            }
        }

        return $images ? $images : null;
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function booths()
    {
        return $this->hasMany(Booth::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Booth::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'seller_followers', 'seller_id', 'followed_by');
    }

    public function isBeingFollowedBy()
    {
        if (!auth('user')->user()) {
            return false;
        }

        return $this->followers()->where('followed_by', auth('user')->user()->id)->exists();
    }

    public function rates()
    {
        return $this->morphMany(Rate::class, 'rateable');
    }

    public function isRatedBy()
    {
        if (!auth('user')->user()) {
            return false;
        }

        return $this->rates()->where('rated_by', auth('user')->user()->id)->exists();
    }

    public function getRatingAttribute()
    {
        $score = $this->rates->sum('score');

        $count = $this->rates()->count();

        if ($count === 0) {
            return 0;
        }

        return ($score) / ($count);
    }
}
