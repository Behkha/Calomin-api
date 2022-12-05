<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'rated_by',
        'score',
    ];

    public $timestamps = false;

    public function ratedBy()
    {
        return $this->belongsTo(User::class, 'rated_by', 'id');
    }

    public function rateable()
    {
        return $this->morphTo();
    }
}
