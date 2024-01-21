<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestResource extends JsonResource
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
            'type' => $this->name,
            'status' => $this->statusName,
            'required' => $this->required,
            'collected' => $this->collected,
            'reward' => $this->reward,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
