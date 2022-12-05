<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public const TRANSACTION_TYPES = [
        'zarinpal' => 1,
        'cash' => 2,
        'wallet' => 3,
    ];

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'is_verified',
        'device',
        'code',
    ];

    public function transactionable()
    {
        return $this->morphTo();
    }
}
