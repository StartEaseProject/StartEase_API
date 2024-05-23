<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Scientific_committee_memberResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'establishment' => $this->establishment_id ? new EstablishmentResource($this->establishment) : null,
            'speciality' => $this->speciality_id ? new SpecialityResource($this->speciality) : null,
            'grade' => $this->grade_id ? new GradeResource($this->grade) : null
        ];
    }
}
