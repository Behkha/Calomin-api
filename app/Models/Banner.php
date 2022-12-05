<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    //
    protected $fillable = [
        'cover',
        'link',
        'index'
    ];

    public function getCoverAttribute($value)
    {
        if (env('APP_ENV') === 'local') {
            return $value;
        }
        return env('STORAGE_PATH') . $value;
    }
}
