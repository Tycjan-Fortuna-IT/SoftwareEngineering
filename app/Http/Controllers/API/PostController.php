<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\PaginationHelper;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Post;
use App\Http\Resources\API\PostResource;
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
            ->when($request->has('withCommentCount'), fn ($query) => $query->withCount('comments'))
            ->allowedFilters([
                'title',
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
}
