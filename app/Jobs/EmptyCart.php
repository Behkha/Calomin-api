<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class EmptyCart
{
    use Dispatchable, SerializesModels;

    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $cart = DB::table('carts')
            ->where('user_id', $this->userId)
            ->first();
        DB::table('cart_product')
            ->where('cart_id', $cart->id)
            ->delete();
    }
}
