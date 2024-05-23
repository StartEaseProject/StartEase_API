<?php

namespace App\Http\Resources;

use App\Models\Defence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefenceResource extends JsonResource
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
                'members' => $this->project->members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        "photo_url" => $member->photo_url,
                        "email" => $member->email,
                        'first_name' => $member->person ? $member->person->first_name : null,
                        'last_name' => $member->person ? $member->person->last_name : null,
                    ];
                }),
            ],
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'time' => Carbon::parse($this->time)->format('H:i'),
            'establishment' => [
                'id' => $this->establishment->id,
                'name' => $this->establishment->name,
                'rooms' => $this->establishment->rooms
            ],
            'guest' => $this->guest,
            'room' => $this->room,
            'other_place' => $this->other_place,
            'mode' => $this->mode,
            'nature' => $this->nature,
            'reserve' => $this->reserve,
            'files' => (object) $this->files ?? (object) [],
            'project_id' => $this->project->id,
            'has_deliberation' => count($this->deliberations)>0,
            'jurys' => $this->jurys_formatted(),
        ];
    }

    private function jurys_formatted(): array
    {
        $formattedJurys = [
            'examiners' => [],
        ];

        foreach ($this->jurys as $jury) {
            $juryAttributes = [
                'email' => $jury->email,
                'photo_url' => $jury->photo_url,
                'id' => $jury->id,
            ];

            $role = $jury->pivot->role;

            if ($role === Defence::JURY_ROLES['PRESIDENT']) {
                $formattedJurys['president'] = $juryAttributes;
            } elseif ($role === Defence::JURY_ROLES['EXAMINER']) {
                array_push($formattedJurys['examiners'], $juryAttributes);
            } elseif ($role === Defence::JURY_ROLES['SUPERVISOR']) {
                $formattedJurys['supervisor'] = $juryAttributes;
            } elseif ($role === Defence::JURY_ROLES['CO_SUPERVISOR']) {
                $formattedJurys['co_supervisor'] = $juryAttributes;
            }
        }
        if (!isset($formattedJurys['co_supervisor'])) {
            $formattedJurys['co_supervisor'] = null;
        }
        return $formattedJurys;
    }
}
