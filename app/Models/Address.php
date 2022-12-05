<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'receiver_name',
        'receiver_phone',
        'receiver_home_phone',
        'receiver_home_phone_prefix',
        'state_id',
        'city_id',
        'zipcode',
        'address',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
