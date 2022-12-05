<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BoothResource;
use App\Http\Resources\CommentResource;
use App\Models\Booth;
use App\Models\Comment;
use App\Models\Rate;
use Illuminate\Http\Request;

class BoothsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user')->except('indexComments');
    }

    public function follow(Booth $booth)
    {
        $user = auth('user')->user();

        if(!$booth->isBeingFollowedBy()) {
            $booth->followers()->attach(['followed_by' => $user->id]);

            return response()->json(['message' => 'added to followers'], 201);
        }

        return response()->json(['errors' => 'already following this booth'], 400);
    }

    public function showFollowing()
    {
        $booths = auth('user')->user()->booths;

        return BoothResource::collection($booths);
    }

    public function unfollow(Booth $booth)
    {
        $user = auth('user')->user();
        if($booth->isBeingFollowedBy()) {
            $booth->followers()->detach(['followed_by' => $user->id]);
            return response()->json(['message' => 'removed from followers']);
        }
        return response()->json(['errors' => 'not following this booth'], 400);
    }

    public function rate(Booth $booth, Request $request)
    {
        $request->validate(['score' => 'required|min:1|max:5']);

        if ($booth->ratedByUser()) {
            return response()->json(['errors' => 'already rated']);
        }

        $booth->rates()->save(new Rate([
            'rated_by' => auth('user')->user()->id,
            'score' => $request->input('score'),
        ]));

        return response()->json(['message' => 'rated successfuly'], 201);
    }

    public function comment(Booth $booth, Request $request)
    {
        $request->validate(['body' => 'required|string|max:255']);

        $booth->comments()->save(new Comment([
            'commented_by' => auth('user')->user()->id,
            'body' => $request->input('body'),
        ]));

        return response()->json(['message' => 'comment added'], 201);
    }

    public function indexComments(Booth $booth)
    {
        $comments = $booth->comments()->paginate();

        return CommentResource::collection($comments);
    }

    public function getFollows()
    {
        $booths = auth('user')->user()->booths;
        return BoothResource::collection($booths);
    }
}
