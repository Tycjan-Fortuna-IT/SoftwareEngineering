<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'user' => new UserInPostResource($this->whenLoaded('user')),
            'comments_count' => $this->when($request->has('withCommentCount'), $this->comments_count),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
