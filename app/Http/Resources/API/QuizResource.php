<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
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
            'result' => $this->result,
            'questions' => QuestionResource::collection($this->questions),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
