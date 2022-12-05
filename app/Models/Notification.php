<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Notification extends Model
{
    public $timestamps = false;

    protected $casts = [
        'is_read' => 'boolean',
    ];

    protected $hidden = [
        'notificationable_type',
        'notificationable_id',
        'notificationable',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Jalalian::forge($value)->ago();
    }

    public function notificationable()
    {
        return $this->morphTo();
    }
}
