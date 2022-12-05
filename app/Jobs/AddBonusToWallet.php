<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Wallet;

class AddBonusToWallet
{
    use Dispatchable, SerializesModels;

    private $userId;

    private $orderId;

    public function __construct($userId, $orderId)
    {
        $this->userId = $userId;
        $this->orderId = $orderId;
    }

    public function handle()
    {
        \DB::transaction(function () {
            $order = Order::find($this->orderId);
            $bonus = 0;
            foreach ($order->products as $product) {
                $bonus += $product->bonus * $product->pivot->quantity;
                $bonus += $product->discount_bonus;
            }
            $wallet = Wallet::where('user_id', $this->userId)
                ->first();
            $wallet->balance += $bonus;
            $wallet->save();
        });
    }
}
