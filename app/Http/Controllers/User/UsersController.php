<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePassword;
use App\Http\Requests\UpdateUserProfile;
use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.user');
        $this->middleware('auth:user');
    }

    public function showMe()
    {
        $user = auth('user')->user();
        return new UserResource($user);
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required_without:delete|file|image|max:5000',
            'delete' => 'boolean',
        ]);

        $user = auth('user')->user();

        if ($request->input('delete')) {
            if ($user->profile_picture) {
                Storage::delete($user->getOriginal('profile_picture'));

                $user->profile_picture = null;

                $user->save();
            }

            return response()->json(['message' => 'profile picture deleted']);
        }

        if ($user->profile_picture) {
            Storage::delete($user->getOriginal('profile_picture'));
        }

        $user->profile_picture = $request->file('profile_picture')->store('users');

        $user->save();

        return new UserResource($user);
    }

    public function updateProfile(UpdateUserProfile $request)
    {
        $user = auth('user')->user();

        $user->update($request->only([
            'name',
            'id_code',
            'home_phone',
            'email',
            'want_notification',
            'birth_date',
            'sex',
            'state_id',
            'city_id',
        ]));

        return response()->json(['message' => 'profile updated']);
    }

    public function updatePassword(UpdatePassword $request)
    {
        $user = auth('user')->user();

        $user->password = Hash::make($request->input('new_password'));

        $user->save();

        return response()->json(['message' => 'password changed successfuly']);
    }
}
