<?php

namespace App\Http\Resources;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tranform = [
            Project::STATUSES['PENDING'] => 'pending',
            Project::STATUSES['ACCEPTED'] => Project::UI_STATUSES['ACCEPTED'],
            Project::STATUSES['REFUSED'] => Project::UI_STATUSES['REFUSED'],
            Project::STATUSES['RECOURSE'] => Project::UI_STATUSES['RECOURSE'],
            Project::STATUSES['RECOURSE_ACCEPTED'] => Project::UI_STATUSES['ACCEPTED'],
            Project::STATUSES['RECOURSE_REFUSED'] => Project::UI_STATUSES['REFUSED']
        ];

        return [
            'id' => $this->id,
            'defence_id' => $this->defence_id,
            'type' => $this->type,
            'trademark_name' => $this->trademark_name,
            'scientific_name' => $this->scientific_name,
            'resume' => $this->resume,
            'status' => $tranform[$this->status],
            'files' => (object) $this->files ?? (object) [],
            'progress' => (object) $this->progress ?? (object) [],
            'submission_date' => Carbon::parse($this->submission_date)->format('Y-m-d'),
            'decision_date' => $this->decision_date ? Carbon::parse($this->decision_date)->format('Y-m-d') : null,
            'recourse_decision_date' => $this->recourse_decision_date ? Carbon::parse($this->recourse_decision_date)->format('Y-m-d') : null,
            'is_authorized_defence' => $this->is_authorized_defence ? true : false,
            'establishment' => [
                'name' => $this->domicile_establishment->name
            ],
            'project_holder' => [
                'id' => $this->project_holder->id,
                "photo_url" => $this->project_holder->photo_url,
                "email" => $this->project_holder->email
            ],
            'supervisor' => [
                'id' => $this->supervisor->id,
                "photo_url" => $this->supervisor->photo_url,
                "email" => $this->supervisor->email
            ],
            'co_supervisor' => !$this->co_supervisor_id ? null :
                [
                    'id' => $this->co_supervisor->id,
                    "photo_url" => $this->co_supervisor->photo_url,
                    "email" => $this->co_supervisor->email
                ],
            'members' => $this->members->map(function ($member) {
                return [
                    'id' => $member->id,
                    "photo_url" => $member->photo_url,
                    "email" => $member->email,
                    'first_name' => $member->person ? $member->person->first_name : null,
                    'last_name' => $member->person ? $member->person->last_name : null,
                ];
            }),
            'updated_at' => $this->updated_at ? $this->updated_at->diffForHumans() : $this->updated_at
        ];
    }
}
