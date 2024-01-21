<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            'limit' => $this->limit,
            'level' => $this->level,
            'stage' => $this->stage,
            'goal' => $this->goal,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
