<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannersIndex extends Model
{
    //
    protected $casts = [
        'values' => 'json'
    ];
}
