<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;

class DiscountCodesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function create(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1',
            'expiration_date' => 'required|string',
            'max' => 'required|integer|min:1',
            'percent' => 'required|integer|min:1|max:100',
        ]);
        $year = explode('-', $request->input('expiration_date'))[0];
        $month = explode('-', $request->input('expiration_date'))[1];
        $day = explode('-', $request->input('expiration_date'))[2];
        $jdate = new Jalalian($year, $month, $day);
        $dcode = \DB::table('discount_codes')
            ->orderBy('group_id', 'desc')
            ->first();
        for ($i = 0; $i < $request->input('count'); $i++) {
            $time = str_replace('.', '', microtime(true));
            $groupId = $dcode ? $dcode->group_id + 1 : 1;
            \DB::table('discount_codes')
                ->insert([
                    'code' => substr($time, -4) . Str::random(15),
                    'expiration_date' => $jdate->toCarbon(),
                    'max' => $request->input('max'),
                    'percent' => $request->input('percent'),
                    'group_id' => $groupId,
                ]);
        }
        return response()->json(['message' => 'ok']);
    }

    public function index(Request $request)
    {
        if ($request->query('percent')) {
            $dcodes = \DB::table('discount_codes')
                ->selectRaw(
                    'group_id AS id, max, percent, expiration_date, created_at, COUNT(*) AS count')
                ->where('percent', $request->query('percent'))
                ->groupBy('group_id')
                ->get();
            foreach ($dcodes as $dcode) {
                $dcode->expiration_date = Jalalian::forge(new Carbon($dcode->expiration_date))->format('%Y-%m-%d');
                $dcode->created_at = Jalalian::forge(new Carbon($dcode->created_at))->format('%Y-%m-%d h:i:s');
            }
            return response()->json(['data' => $dcodes]);
        }
        $dcodes = \DB::table('discount_codes')
            ->selectRaw('group_id AS id, max, percent, expiration_date, created_at, COUNT(*) AS count')
            ->groupBy('group_id')
            ->get();
        foreach ($dcodes as $dcode) {
            $dcode->expiration_date = Jalalian::forge(new Carbon($dcode->expiration_date))->format('%Y-%m-%d');
            $dcode->created_at = Jalalian::forge(new Carbon($dcode->created_at))->format('%Y-%m-%d h:i:s');
        }
        return response()->json(['data' => $dcodes]);
    }

    public function show($id)
    {
        $dcodes = \DB::table('discount_codes')
            ->where('group_id', $id)
            ->get();
        foreach ($dcodes as $dcode) {
            $dcode->expiration_date = Jalalian::forge(new Carbon($dcode->expiration_date))->format('%Y-%m-%d');
            $dcode->created_at = Jalalian::forge(new Carbon($dcode->created_at))->format('%Y-%m-%d h:i:s');
        }
        return response()->json(['data' => $dcodes]);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'expiration_date' => 'required|string',
            'max' => 'required|integer|min:1',
            'percent' => 'required|integer|min:1|max:100',
        ]);
        $year = explode('-', $request->input('expiration_date'))[0];
        $month = explode('-', $request->input('expiration_date'))[1];
        $day = explode('-', $request->input('expiration_date'))[2];
        $jdate = new Jalalian($year, $month, $day);
        $request->merge(['expiration_date' => $jdate->toCarbon()]);
        $used = \DB::table('discount_codes')
            ->where('group_id', $id)
            ->where('is_used', true)
            ->exists();
        if ($used) {
            return response()->json(['message' => 'can not delete'], 400);
        }
        \DB::table('discount_codes')
            ->where('group_id', $id)
            ->update($request->only(['count', 'expiration_date', 'max', 'percent']));
        return response()->json(['message' => 'ok']);
    }
}
