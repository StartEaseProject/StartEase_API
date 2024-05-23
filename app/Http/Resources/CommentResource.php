<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => [
                'id' => $this->user->id,
                "photo_url" => $this->user->photo_url,
                "person" => [
                    "first_name" => $this->user->person->first_name,
                    "last_name" => $this->user->person->last_name,
                ]
            ],
            'replies' => CommentResource::collection($this->replies),
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at ? $this->updated_at->diffForHumans() : $this->updated_at
        ];
    }
}
