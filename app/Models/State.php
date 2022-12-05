<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public $timestamps = false;

    //one-to-many
    public function cities()
    {
        return $this->hasMany(City::class)->orderBy('name', 'asc');
    }
}
