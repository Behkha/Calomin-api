<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSeller;
use App\Http\Resources\BoothResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\Seller as SellerResource;
use App\Models\Comment;
use App\Models\Rate;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellersController extends Controller
{
    public function __construct(Request $request)
    {
        if ($request->header('device') === 'mobile') {
            $this->middleware('auth:user');
        }

        $this->middleware('auth:user')->only([
            'follow',
            'unfollow',
            'rate',
            'addComment',
        ]);

        $this->middleware('auth:admin')->only([
            'create',
            'update',
            'updateMedia',
        ]);
    }

    public function index(Request $request)
    {
        $query = Seller::query();

        if ($request->query('highest_rating')) {
            $query->withCount(['rates as average_rating' => function ($builder) {
                $builder->select(\DB::raw('coalesce(avg(score),0)'));
            }])->orderByDesc('average_rating');
        } else {
            $query->orderBy('id', 'asc');
        }

        $sellers = $query->paginate();

        return SellerResource::collection($sellers);
    }

    public function show(Seller $seller)
    {
        return new SellerResource($seller);
    }

    public function create(CreateSeller $request)
    {
        $seller = new Seller($request->only([
            'name',
            'story',
            'state_id',
            'city_id',
        ]));

        if ($request->hasFile('profile_picture')) {
            $seller->profile_picture = $request->file('profile_picture')->store('sellers');
        }

        if ($request->hasFile('gallery')) {
            $gallery = [];

            foreach ($request->file('gallery') as $image) {
                array_push($gallery, $image->store('sellers'));
            }

            $seller->gallery = $gallery;
        }

        $seller->save();

        return new SellerResource($seller);
    }

    public function update(Seller $seller, CreateSeller $request)
    {
        $request->update($request->only([
            'name',
            'story',
            'state_id',
            'city_id',
        ]));

        return new SellerResource($seller);
    }

    public function booths(Request $request, Seller $seller)
    {
        if ($request->query('all')) {

            return BoothResource::collection($seller->booths->load('seller'));
        }

        $booths = $seller->booths()
            ->paginate()
            ->load('seller');

        return BoothResource::collection($booths);
    }

    public function products(Request $request, Seller $seller)
    {
        if ($request->query('all')) {

            return ProductResource::collection($seller->products);
        }

        $products = $seller->products()
            ->paginate();

        return ProductResource::collection($products);
    }

    public function addComment(Request $request, Seller $seller)
    {
        $request->validate(['body' => 'required|string|max:255']);

        $comment = $seller->comments()->save(new Comment([
            'commented_by' => auth('user')->user()->id,
            'body' => $request->input('body'),
        ]));

        return new CommentResource($comment);
    }

    public function showComments(Request $request, Seller $seller)
    {
        $comments = Comment::where('commentable_type', Seller::class)
            ->where('commentable_id', $seller->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return CommentResource::collection($comments);
    }

    public function follow(Seller $seller)
    {
        if (!$seller->isBeingFollowedBy()) {
            $seller->followers()->attach(auth('user')->user()->id);

            return response()->json(['message' => 'success'], 201);
        }

        return response()->json(['errors' => 'already following'], 400);
    }

    public function unfollow(Seller $seller)
    {
        if ($seller->isBeingFollowedBy()) {
            $seller->followers()->detach(auth('user')->user()->id);

            return response()->json(['message' => 'success']);
        }

        return response()->json(['errors' => 'not following'], 400);
    }

    public function rate(Seller $seller, Request $request)
    {
        if ($seller->isRatedBy()) {
            return response()->json(['errors' => 'already rated'], 400);
        }

        $request->validate(['score' => 'required|min:1|max:5']);

        $seller->rates()->save(new Rate([
            'rated_by' => auth('user')->user()->id,
            'score' => $request->input('score'),
        ]));

        return response()->json(['message' => 'success'], 201);
    }
}
