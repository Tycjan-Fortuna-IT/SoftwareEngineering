<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\PaginationHelper;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Comment;
use App\Http\Resources\API\CommentResource;
use Spatie\QueryBuilder\AllowedFilter;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $comments = QueryBuilder::for(Comment::class)
            ->allowedFilters([
                AllowedFilter::scope('user_uuid'),
            ])
            ->allowedSorts([
                'created_at',
                'updated_at',
            ]);


        PaginationHelper::Paginate($comments, $request);

        return CommentResource::collection($comments);
    }
}
