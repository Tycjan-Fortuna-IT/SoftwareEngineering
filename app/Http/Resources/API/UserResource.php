<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'avatar' => $this->avatar,
            'about' => $this->about,
            'email' => $this->email,
            'level' => (int)$this->level,
            'experience' => (int)$this->experience,
            'anonymous' => (bool)$this->anonymous,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
