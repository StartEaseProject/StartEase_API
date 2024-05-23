<?php

namespace App\Http\Repositories;

use App\Http\Requests\User\CompleteRegister\TeacherRequest;
use App\Http\Requests\User\InitialRegisterRequest;
use App\Models\Establishment;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Speciality;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherRepository
{
    private String $req = TeacherRequest::class;
    public function __construct(
        private User $user, 
        private Teacher $teacher, 
        private Establishment $establishment, 
        private Speciality $speciality,
        private Grade $grade,
        private Role $role
    ){}

    
    public function register(InitialRegisterRequest $request)
    {
        $hash = $this->user::generateHash();
        $user = null;

        DB::transaction(function () use ($request, &$user, $hash) {
            $role = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES['TEACHER']);
            $user = $this->user::create([
                'email' => $request->email,
                'register_verification_hash' => $hash,
                'person_type' => $this->teacher::class,
            ]);
            $user->roles()->attach($role->id);
        });
        return $user;
    }

    public function completeRegister(Request $request): Teacher
    {
        $req = new $this->req();
        $validator = Validator::make($request->all(), $req->rules());
        if ($validator->fails()) {
            throw new HttpResponseException(
                new JsonResponse(
                    [
                        'success' => false,
                        'message' => $validator->errors()->first(),
                        'errors' => $validator->errors(),
                    ],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                )
            );
        }
        $validatedData = $validator->validated();
        $person = $this->teacher::create($validatedData);
        return $person;
    }

    public function getReferences(): array
    {
        return [
            'establishments' => $this->establishment::all(),
            'grades' => $this->grade::where('type', Speciality::TYPES['TEACHER'])->get(),
            'specialities' => $this->speciality::where('type', Speciality::TYPES['TEACHER'])->get(),
        ];
    }

    public function create_supervisor($email): User 
    {
        $hash = $this->user::generateHash();
        $role = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES["SUPERVISOR"]);
        $role2 = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES['TEACHER']);
        $user = $this->user::create([
            'email' => $email,
            'register_verification_hash' => $hash,
            'person_type' => $this->teacher::class,
        ]);
        $user->roles()->syncWithoutDetaching([$role->id, $role2->id]);
        $user->refresh();
        return $user;
    }

    public function create_jury($email): User 
    {
        $hash = $this->user::generateHash();
        $role = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES['TEACHER']);
        $user = $this->user::create([
            'email' => $email,
            'register_verification_hash' => $hash,
            'person_type' => $this->teacher::class,
        ]);
        $user->roles()->syncWithoutDetaching([$role->id]);
        $user->refresh();
        return $user;
    }
}
