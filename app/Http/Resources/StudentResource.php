<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'num_inscription' => $this->num_inscription,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'birthday' => $this->birthday,
            'birth_place' => $this->birth_place,
            'establishment' => $this->establishment ? new EstablishmentResource($this->establishment) : null,
            'speciality' => $this->speciality ? new SpecialityResource($this->speciality) : null,
            'filiere' => $this->filiere ? new FiliereResource($this->filiere) : null
        ];
    }
}
