<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')
            ->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        $subjects = DB::table('subjects')
            ->get();
        return response()->json(['data' => $subjects]);
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255|unique:subjects']);
        $subject = DB::table('subjects')
            ->insert(['title' => $request->input('title')]);
        return response()->json(['data' => $subject], 201);
    }

    public function show($id)
    {
        $subject = DB::table('subjects')
            ->where('id', $id)
            ->first();
        if ($subject) {
            return response()->json(['data' => $subject]);
        }
        throw new ModelNotFoundException();
    }

    public function update(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255|unique:subjects']);
        $subject = DB::table('subjects')
            ->where('id', $id)
            ->first();
        if ($subject) {
            DB::table('subjects')
                ->where('id', $id)
                ->update(['title' => $request->input('title')]);
            return response()->json(['data' => $subject]);
        }
        throw new ModelNotFoundException();
    }

    public function destroy($id)
    {
        $subject = DB::table('subjects')
            ->where('id', $id)
            ->first();
        if ($subject) {
            DB::table('subjects')
                ->where('id', $id)
                ->delete();
            return response()->json(['data' => $subject]);
        }
        throw new ModelNotFoundException();
    }
}
