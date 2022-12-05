<?php

namespace App\Http\Resources;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        $remainingTime = Carbon::now()
            ->diffInSeconds($this->getOriginal('created_at'));
        $setting = DB::table('settings')
            ->first();
        $order = Order::find($this->id);
        if ($setting->order_cancel_time < $remainingTime &&
            $order->status == Order::STATUS['unknown']
        ) {
            $order->status = Order::STATUS['canceled'];
            $order->save();
            $remainingTime = 0;
        } else {
            $remainingTime = $setting->order_cancel_time - $remainingTime;
        }
        if ($this->status_code != 1 && $this->status_code != 9) {
            $remainingTime = 0;
        }
        $paymentMethodFa;
        switch ($this->payment_method) {
            case 'internet':
                $paymentMethodFa = 'اینترنتی';
                break;
            case 'cash':
                $paymentMethodFa = 'نقدی';
                break;
            case 'wallet':
                $paymentMethodFa = 'کیف پول';
                break;
        }
        return [
            'id' => $this->id,
            'code' => $this->code,
            'user' => new User($this->whenLoaded('user')),
            'delivery_method' => $this->deliveryMethod,
            'status' => $this->status,
            'status_code' => $this->status_code,
            'address' => new Address($this->address),
            'products' => Product::collection($this->whenLoaded('products')),
            'price' => $this->price,
            'price_after_discount' => $this->price_after_discount,
            'payment_type' => $paymentMethodFa,
            'created_at' => $this->created_at,
            'price_before_discount' => $this->price_before_discount,
            'remaining_time' => $remainingTime,
            'total_discount' => $this->price_before_discount - $this->price_after_discount,
            'total_bonus' => $this->total_bonus,
            'total_price_plus_bonus' => $this->price_after_discount ? $this->price_after_discount - $this->total_bonus : $this->price - $this->total_bonus,
            'total_mass' => $this->total_mass,
            'total_tax' => $this->total_tax,
        ];
    }
}
