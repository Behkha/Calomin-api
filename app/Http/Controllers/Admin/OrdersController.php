<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Jobs\EmptyCart;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        if ($request->query('code')) {
            $order = Order::where('code', $request->query('code'))
                ->first();
            if ($order) {
                return new OrderResource($order);
            } else {
                return response()->json(['data' => '']);
            }
        }
        $orders = Order::orderBy('created_at', 'desc')->paginate();
        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        $order->load('user', 'products');

        return new OrderResource($order);
    }

    public function update(Order $order, Request $request)
    {
        // reject order
        if ($request->query('reject') && $order->getOriginal('status') === Order::STATUS['registered']) {
            $order->status = Order::STATUS['rejected'];
            $order->save();
            return response()->json(['message' => 'order rejected']);
        }

        if ($order->getOriginal('status') === Order::STATUS['delivered']) {
            return response()->json(['errors' => 'invalid order'], 400);
        }

        if ($order->getOriginal('status') === Order::STATUS['registered'] && $order->transaction->type === Transaction::TRANSACTION_TYPES['cash']) {
            $order->status = Order::STATUS['verified'];
            $wallet = Wallet::where('user_id', $order->user_id)
                ->first();
            $bonus = 0;
            foreach ($order->products as $product) {
                $product->quantity -= $product->pivot->quantity;
                $bonus += $product->bonus * $product->pivot->quantity;
                $bonus += $product->discount_bonus;
                $product->counter_sales += $product->pivot->quantity;
                $product->save();
            }
            $wallet->balance += $bonus;
            $wallet->save();
            EmptyCart::dispatchNow($order->user_id);
        }

        if ($order->getOriginal('status') === Order::STATUS['registered'] &&
            $order->transaction->type === Transaction::TRANSACTION_TYPES['zarinpal']) {
            $order->status = Order::STATUS['verified'];
        }

        if ($order->getOriginal('status') === Order::STATUS['verified']) {
            $order->status = Order::STATUS['package'];
        }

        if ($order->getOriginal('status') === Order::STATUS['package']) {
            $order->status = Order::STATUS['sent'];
        }

        if ($order->getOriginal('status') === Order::STATUS['sent']) {
            $order->status = Order::STATUS['delivered'];
            if ($order->transaction->type === Transaction::TRANSACTION_TYPES['cash']) {
                $order->transaction->is_verified = true;
                $order->transaction->save();
                foreach ($order->products as $product) {
                    $product->counter_sales += $product->pivot->quantity;
                    $product->save();
                }
            }
        }

        $order->save();
    }
}
