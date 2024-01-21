<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\PaginationHelper;
use App\Http\Resources\API\PostResource;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $posts = QueryBuilder::for(Post::class)
            ->when($request->has('withAuthor'), fn ($query) => $query->with('user'))
            ->when($request->has('withCommentCount'), fn ($query) => $query->withCount('comments'))
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::scope('user_uuid'),
            ])
            ->allowedSorts([
                'created_at',
                'updated_at',
            ]);

        PaginationHelper::Paginate($posts, $request);

        return PostResource::collection($posts);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return PostResource
     */
    public function show(Post $post): PostResource
    {
        $post->load(['comments', 'user']);

        return new PostResource($post);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'nullable|string',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $user = Auth::user();

        $post = Post::create([
            'image' => $request->image ?? null,
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => new PostResource($post),
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        $request->validate([
            'image' => 'nullable|string',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $post->image = $request->image ?? $post->image;
        $post->title = $request->title ?? $post->title;
        $post->description = $request->description ?? $post->description;

        $post->save();

        return response()->json(['message' => 'Post updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        $post->comments()->delete();
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
