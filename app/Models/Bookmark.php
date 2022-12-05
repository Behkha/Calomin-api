<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    public function bookmarkable()
    {
        return $this->morphTo();
    }

    public $timestamps = false;
}
