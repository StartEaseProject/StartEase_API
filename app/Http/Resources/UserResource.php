<?php

namespace App\Http\Resources;

use App\Models\Headmaster;
use App\Models\Internship_service_member;
use App\Models\Scientific_committee_member;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
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
        $typesToResources = [
            Student::class => new StudentResource($this->person),
            Teacher::class => new TeacherResource($this->person),
            Scientific_committee_member::class => new Scientific_committee_memberResource($this->person),
            Internship_service_member::class => new Internship_service_memberResource($this->person),
            Headmaster::class => new HeadmasterResource($this->person),
        ];
    
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'photo_url' => $this->photo_url,
            'is_enabled' => $this->is_enabled,
            'person_type' => $this->person_type ? User::MODELSTOTYPES[$this->person_type] : null,
            'person' => $this->person_id ? $typesToResources[$this->person_type] : null,
            'roles' => $this->roles->map(function ($role) {
                return $role->only(['id', 'name']);
            }),
            'permissions' => $this->permissions->map(function ($perm) {
                return $perm->only(['id', 'name']);
            }),
            'is_project_member' => $this->member_project ? true : false
        ];
    }
}
