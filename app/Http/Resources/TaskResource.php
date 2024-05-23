<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'refusal_motif' => $this->refusal_motif,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'submission_date' => $this->submission_date ? Carbon::parse($this->submission_date)->format('Y-m-d') : null,
            'submission_description' => $this->submission_description,
            'completed_date' => $this->completed_date ? Carbon::parse($this->completed_date)->format('Y-m-d') : null,
            'resources' => $this->resources,
            'project_id' => $this->project_id,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at ? $this->updated_at->diffForHumans() : $this->updated_at
        ];
    }
}
