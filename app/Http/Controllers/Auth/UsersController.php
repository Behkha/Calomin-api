<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePassword;
use App\Http\Requests\ForgetPassword;
use App\Http\Requests\SigninUser;
use App\Http\Requests\SignupUser;
use App\Http\Requests\VerifyForgetPassword;
use App\Http\Requests\VerifyPhone;
use App\Models\Cart;
use App\Models\PasswordReset;
use App\Models\Phone;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function signup(SignupUser $request)
    {
        $result = $this->generateToken($request, 'signup');

        if (!$result instanceof Phone) {
            return $result;
        }

        $user = User::updateOrCreate(['phone' => $request->phone], $request->all());

        if (env('APP_ENV') === 'production') {
//            $url = 'http://webone-sms.ir/SMSInOutBox/SendSms';
//            $query_array = array(
//                'username' => env('SMS_USERNAME'),
//                'password' => env('SMS_PASSWORD'),
//                'from' => env('SMS_PHONE'),
//                'to' => $request->phone,
//                'text' => 'کد فعالسازی کالومین : ' . $result->token,
//            );
//            $query = http_build_query($query_array);
//            $result = file_get_contents($url . '?' . $query);
//            return response()->json(['message' => 'token sent'], 201);

            $url = 'http://www.afe.ir/Url/SendSMS';
            $query_array = array(
                'Username' => env('SMS_USERNAME'),
                'Password' => env('SMS_PASSWORD'),
                'Number' => env('SMS_PHONE'),
                'Mobile' => $request->phone,
                'SMS' => 'به%20کالومین%20خوش%20آمدید%0Aکد%20فعاسازی:' . $result->token,
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . 'Username=' . $query_array['Username'] . '&Password=' . $query_array['Password'] . '&Number=' . $query_array['Number'] . '&Mobile=' . $query_array['Mobile'] . '&SMS=' . $query_array['SMS']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $data = curl_exec($ch);
            curl_close($ch);
            return response()->json(['message' => 'token sent'], 201);

        }

        return response()->json(['data' => ['token' => $result->token]], 201);
    }

    public function signin(SigninUser $request)
    {
        $user = User::where('email', $request->username)
            ->orWhere('phone', $request->username)
            ->first();

        if (!$user || !$user->is_verified) {
            return response()->json(['errors' => 'invalid input'], 400);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['errors' => 'invalid input'], 400);
        }

        return response()->json(['data' => ['token' => auth('user')->login($user)]], 200);
    }

    public function verifyPhone(VerifyPhone $request)
    {
        $phone = Phone::where('number', $request->input('phone'))
            ->latest()
            ->first();

        $phone->is_verified = true;

        $user = User::where('phone', $phone->number)
            ->first();

        $user->is_verified = true;

        $user->registered_at = now()->toDateString();

        $phone->save();
        $user->save();

        Wallet::create(['user_id' => $user->id]);

        Cart::create(['user_id' => $user->id]);
        return response()->json([
            'data' => [
                'token' => auth('user')->login($user),
            ],
        ]);
    }

    public function forgetPassword(ForgetPassword $request)
    {
        $reset = $this->generateToken($request, 'forget');

        if (!$reset instanceof PasswordReset) {
            return $reset;
        }

        if (env('APP_ENV') === 'production') {
            //\Smsirlaravel::send('کد فراموشی  حساب کالومین : ' . $reset->token, $request->phone);
            $url = 'http://www.afe.ir/Url/SendSMS';
            $query_array = array(
                'Username' => env('SMS_USERNAME'),
                'Password' => env('SMS_PASSWORD'),
                'Number' => env('SMS_PHONE'),
                'Mobile' => $request->phone,
                'SMS' => 'به%20کالومین%20خوش%20آمدید%0Aکد%20فراموشی:' . $reset->token,
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . 'Username=' . $query_array['Username'] . '&Password=' . $query_array['Password'] . '&Number=' . $query_array['Number'] . '&Mobile=' . $query_array['Mobile'] . '&SMS=' . $query_array['SMS']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $data = curl_exec($ch);
            curl_close($ch);
            return response()->json(['message' => 'token sent'], 201);
        }

        return response()->json(['data' => ['token' => $reset->token]], 201);
    }

    public function verifyForgetPassword(VerifyForgetPassword $request)
    {
        $reset = PasswordReset::where('phone', $request->phone)
            ->latest()
            ->first();

        $reset->is_verified = true;

        $reset->save();

        return response()->json(['message' => 'password reset verified'], 200);
    }

    public function changePassword(ChangePassword $request)
    {
        $reset = PasswordReset::where('phone', $request->input('phone'))
            ->latest()
            ->first();

        $user = User::where('phone', $request->input('phone'))
            ->first();

        $user->password = $request->input('password');

        $reset->is_used = true;

        $user->save();

        $reset->save();

        return response()->json(['message' => 'password changed successfully']);
    }

    /*
     * --------------------------------------------------------------------
     * Secondary Methods
     * --------------------------------------------------------------------
     */

    private function generateToken($request, $switch)
    {
        if ($switch === 'signup') {
            $canNotSend = Phone::where('number', $request->input('phone'))
                ->where('created_at', '>', now()->subSeconds(120)->toDateTimeString())
                ->exists();

            if ($canNotSend) {
                return response()->json(['errors' => 'must wait 120 seconds'], 400);
            }

            $token = rand(1000, 9999);

            $phone = Phone::create([
                'number' => $request->input('phone'),
                'token' => $token,
            ]);

            return $phone;
        } elseif ($switch === 'forget') {
            $canNotSend = PasswordReset::where('phone', $request->phone)
                ->where('created_at', '>', now()->subSeconds(120)->toDateTimeString())
                ->exists();

            if ($canNotSend) {
                return response()->json(['errors' => 'must wait 120 seconds'], 400);
            }

            $token = rand(1000, 9999);

            $reset = PasswordReset::create([
                'phone' => $request->phone,
                'token' => $token,
            ]);

            return $reset;
        }
    }
}
