<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeriodResource extends JsonResource
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
            'name' => $this->name,
            'start_date' => Carbon::parse($this->start_date)->format('Y-m-d'),
            'end_date' => Carbon::parse($this->end_date)->format('Y-m-d'),
            'establishment' => new EstablishmentResource($this->establishment)
        ];
    }
}
