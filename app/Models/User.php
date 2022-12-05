<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Morilog\Jalali\Jalalian;
use Tymon\JWTAuth\Contracts\JWTSubject;

class   User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name',
        'id_code',
        'home_phone',
        'want_notification',
        'birth_date',
        'sex',
        'state_id',
        'city_id',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    protected $casts = [
        'want_notification' => 'boolean',
    ];

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    //profile_picture accessor
    public function getProfilePicture($value)
    {
        return $value ? env('STORAGE_PATH') . $value : null;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public const SEX = [
        'male' => 1,
        'female' => 2,
    ];

    //sex mutator
    public function setSexAttribute($sex)
    {
        $this->attributes['sex'] = self::SEX[$sex];
    }

    // birth_date mutator
    public function setBirthDateAttribute($value)
    {
        $date = (new Jalalian(explode('-', $value)[0], explode('-', $value)[1], explode('-', $value)[2]))
            ->toCarbon()
            ->toDateString();
        $this->attributes['birth_date'] = $date;
    }

    // birth_date accessor
    public function getBirthDateAttribute($value)
    {
        return jdate($value)->format('%Y-%m-%d');
    }

    //profile_picture accessor
    public function getProfilePictureAttribute($value)
    {
        return $value ? env('STORAGE_PATH') . $value : null;
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function productBookmarks()
    {
        return $this->hasMany(Bookmark::class)->where('bookmarkable_type', Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function booths()
    {
        return $this->belongsToMany(Booth::class, 'booth_followers', 'followed_by', 'booth_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function categoryBookmarks()
    {
        $members = Redis::SMEMBERS('user:' . $this->id . ':bookmarks');

        $categories = collect();

        foreach ($members as $member) {
            if (explode(':', $member)[0] === 'category') {
                $category = Category::find(explode(':', $member)[1]);

                $categories->push($category);
            }
        }

        return $categories;
    }
}
