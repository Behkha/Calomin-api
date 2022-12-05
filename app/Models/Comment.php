<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'commented_by',
        'body',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function commentedBy()
    {
        return $this->belongsTo(User::class, 'commented_by', 'id');
    }
}
