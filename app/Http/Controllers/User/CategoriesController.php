<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category as CategoryResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.user');
        $this->middleware('auth:user');
    }

    public function bookmark(Request $request)
    {
        $this->validateBookmark($request);

        $user = auth('user')->user();

        foreach ($request->categories as $category) {
            Redis::SADD('user:' . $user->id . ':bookmarks', 'category:' . $category);
        }

        return response()->json(['message' => 'added to bookmarks'], 201);
    }

    public function index()
    {
        $user = auth('user')->user();

        $categories = $user->categoryBookmarks();

        return CategoryResource::collection($categories);
    }

    public function deleteBookmark(Request $request)
    {
        $this->validateBookmark($request);

        $user = auth('user')->user();

        foreach ($request->categories as $category) {
            Redis::SREM('user:' . $user->id . ':bookmarks', 'category:' . $category);
        }

        return response()->json(['message' => 'removed from bookmarks']);
    }

    // return categories which has not been bookmarked
    public function remaining()
    {
        $user = auth('user')->user();

        $categories = $user->categoryBookmarks();

        $notBookmarked = Category::whereNotIn('id', $categories->pluck('id'))
            ->paginate();

        return CategoryResource::collection($notBookmarked);
    }

    /*
     * -------------------------------------------------------------------------
     * Secondary Methods
     * -------------------------------------------------------------------------
     */

    private function validateBookmark($request)
    {
        $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|distinct|exists:categories,id',
        ]);
    }
}
