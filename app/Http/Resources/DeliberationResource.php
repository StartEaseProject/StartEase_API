<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliberationResource extends JsonResource
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
            'project' => [
                'trademark_name' => $this->project->trademark_name,
                'scientific_name' => $this->project->scientific_name,
            ],
            'members' => $this->deliberations->map(function($member){
                return [
                    'id' => $member->id,
                    'email' => $member->email,
                    'photo_url' => $member->photo_url,
                    'first_name' => $member->person->first_name,
                    'last_name' => $member->person->last_name,
                    'mark' => $member->pivot->mark,
                    'mention' => $member->pivot->mention,
                    'appreciation' => $member->pivot->appreciation,
                    'diploma_url' => $member->pivot->diploma_url,
                ];
            }),
            'reserves' => $this->reserves
        ];
    }
}
