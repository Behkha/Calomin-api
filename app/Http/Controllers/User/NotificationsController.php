<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
    }

    public function index()
    {
        $user = auth('user')->user();

        $notifications = Notification::where('user_phone', $user->phone)
            ->with('notificationable')
            ->orderBy('is_read', 'asc')
            ->paginate();

        $this->readNotifications($notifications);

        foreach ($notifications as $notification) {

            if ($notification->notificationable_type === \App\Models\Product::class) {
                $product = Product::find($notification->notificationable->id);
                $notification->type = new ProductResource($product);
            }
        }

        return $notifications;
    }

    /*
    * -------------------------------------------------------------------------------------
    * Secondary Methods
    * -------------------------------------------------------------------------------------
    */

    private function readNotifications($notifications)
    {
        foreach ($notifications as $notification) {

            if (! $notification->is_read) {

                $notification->is_read = true;

                $notification->save();
            }
        }
    }
}
