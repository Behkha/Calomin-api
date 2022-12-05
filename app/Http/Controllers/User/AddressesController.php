<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAddress;
use App\Http\Resources\Address as AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressesController extends Controller
{
        public function __construct()
        {
            $this->middleware('auth.user');
            $this->middleware('auth:user');
        }

    public function store(CreateAddress $request)
    {
        $user = auth('user')->user();
        if ($request->input('is_default')) {
            $addr = Address::where('is_default', true)
                ->where('user_id', auth('user')->user()->id)
                ->first();
            if ($addr) {
                $addr->is_default = false;
                $addr->save();
            }
        }
        $address = $user->addresses()->save(new Address([
            'receiver_name' => $request->input('receiver_name'),
            'receiver_phone' => $request->input('receiver_phone'),
            'receiver_home_phone' => $request->input('receiver_home_phone'),
            'receiver_home_phone_prefix' => $request->input('receiver_home_phone_prefix'),
            'state_id' => $request->input('state_id'),
            'city_id' => $request->input('city_id'),
            'zipcode' => $request->input('zipcode'),
            'address' => $request->input('address'),
            'is_default' => $request->input('is_default') ? true : false,
        ]));

        return new AddressResource($address);
    }

    public function index(Request $request)
    {
        $user = auth('user')->user();
        if ($request->query('default')) {
            $address = Address::where('user_id', $user->id)->where('is_default', true)->first();
            if (!$address) {
                return response()->json(['data' => '']);
            }
            return new AddressResource($address);
        }

        $addresses = $user->addresses;

        return AddressResource::collection($addresses);
    }

    public function show($id)
    {
        $address = Address::where('user_id', auth('user')->user()->id)
            ->findOrFail($id);

        return new AddressResource($address);
    }

    public function update(CreateAddress $request, $id)
    {
        $address = Address::find($id);
        if ($request->input('is_default')) {
            $addr = Address::where('is_default', true)
                ->where('user_id', auth('user')->user()->id)
                ->first();
            if ($addr) {
                $addr->is_default = false;
                $addr->save();
            }
        }
        $address->update([
            'receiver_name' => $request->input('receiver_name'),
            'receiver_phone' => $request->input('receiver_phone'),
            'receiver_home_phone' => $request->input('receiver_home_phone'),
            'receiver_home_phone_prefix' => $request->input('receiver_home_phone_prefix'),
            'state_id' => $request->input('state_id'),
            'city_id' => $request->input('city_id'),
            'zipcode' => $request->input('zipcode'),
            'address' => $request->input('address'),
            'is_default' => $request->input('is_default') ? true : false,
        ]);
        return new AddressResource($address);
    }

    public function destroy($id)
    {
        $address = Address::where('user_id', auth('user')->user()->id)
            ->findOrFail($id);

        $address->delete();

        return new AddressResource($address);
    }
}
