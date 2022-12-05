<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryMethod;

class DeliveryMethodsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
    }

    public function index()
    {
        $deliveryMethods = DeliveryMethod::all();

        return response()->json([
            'data' => $deliveryMethods,
        ]);
    }
}
