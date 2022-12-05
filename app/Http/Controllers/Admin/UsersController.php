<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        if ($request->input('search')) {
            $query = User::where('name', 'like', '%' . $request->input('search') . '%')
                ->orWhere('email', 'like', '%' . $request->input('search') . '%')
                ->orWhere('phone', 'like', '%' . $request->input('search') . '%');
        } else {
            $query = User::orderBy('id', 'asc');
        }
        if ($request->query('email')) {
            $user = User::where('email', $request->query('email'))
                ->first();
            if ($user) {
                return new UserResource($user);
            } else {
                return response()->json(['data' => '']);
            }
        }
        if ($request->query('phone')) {
            $user = User::where('phone', $request->query('phone'))
                ->first();
            if ($user) {
                return new UserResource($user);
            } else {
                return response()->json(['data' => '']);
            }
        }
        $users = $query->paginate();

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }
}
