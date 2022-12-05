<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactUsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')
            ->only(['index', 'show', 'delete']);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|digits:11',
            'subject_id' => 'required|exists:subjects,id',
            'body' => 'required|string|max:2000',
        ]);
        DB::table('contact_us')
            ->insert($request->only(['name', 'email', 'phone', 'subject_id', 'body']));
        return response()->json(['data' => 'created'], 201);
    }

    public function index()
    {
        $data = DB::table('contact_us')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        foreach ($data as $d) {
            $subject = DB::table('subjects')
                ->where('id', $d->subject_id)
                ->first();
            if ($subject) {
                $d->subject = $subject->title;
            }
        }
        return response()->json($data);
    }

    public function show($id)
    {
        $data = DB::table('contact_us')
            ->where('id', $id)
            ->first();
        if ($data) {
            $subject = DB::table('subjects')
                ->where('id', $data->subject_id)
                ->first();
            if ($subject) {
                $data->subject = $subject->title;
            }
            return response()->json(['data' => $data]);
        }
        throw new ModelNotFoundException();
    }

    public function delete($id)
    {
        $data = DB::table('contact_us')
            ->where('id', $id)
            ->first();
        if ($data) {
            DB::table('contact_us')
                ->where('id', $id)
                ->delete();
            return response()->json(['data' => $data]);
        }
        throw new ModelNotFoundException();
    }
}
