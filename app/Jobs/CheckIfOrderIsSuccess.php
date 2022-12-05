<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class CheckIfOrderIsSuccess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle()
    {
        $order = Order::find($this->id);

        if (! $order->is_success) {
            $products = $order->products;

            foreach ($products as $product) {
                $products->quantity += $products->pivot->quantity;

                $product->save();
            }
        }
    }
}
