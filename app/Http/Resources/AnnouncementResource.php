<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
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
            'title'=> $this->title,
            'description'=> $this->description,
            'establishment'=> new EstablishmentResource($this->establishment),
            'location'=> $this->location,
            'photo'=> $this->photo,
            'date'=> $this->date ? Carbon::parse($this->date)->format('Y-m-d') : null,
            'start_date'=>  $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d') : null,
            'end_date'=> $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d') : null,
            'type'=> $this->type,
            'visibility'=> $this->visibility,
            'created_at' => $this->created_at ? $this->created_at->diffForHumans() : $this->created_at,
            'updated_at' => $this->updated_at ? $this->updated_at->diffForHumans() : $this->updated_at
        ];
    }
}
