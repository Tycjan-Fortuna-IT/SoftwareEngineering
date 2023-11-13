<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\PaginationHelper;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Post;
use App\Http\Resources\API\PostResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'image' => $this->image,
            'title' => $this->title,
            'description' => $this->description,
            'comments_count' => $this->when($request->has('withCommentCount'), $this->comments_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
