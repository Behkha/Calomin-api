<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;

class LocationsController extends Controller
{
    public function states(Request $request)
    {
        $query = State::orderBy('id', 'asc');

        if ($request->input('search')) {
            $query = State::where('name', 'like', '%' . $request->input('search') . '%');
        }

        $states = $query->get();

        return response()->json([
            'data' => $states,
        ]);
    }

    public function cities(State $state)
    {
        $state->cities = $state->cities;

        return response()->json([
            'data' => $state,
        ]);
    }
}
