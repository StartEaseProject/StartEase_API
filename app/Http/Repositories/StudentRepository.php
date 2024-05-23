<?php

namespace App\Http\Repositories;

use App\Http\Requests\User\CompleteRegister\StudentRequest;
use App\Http\Requests\User\InitialRegisterRequest;
use App\Models\Establishment;
use App\Models\Filiere;
use App\Models\Role;
use App\Models\Speciality;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentRepository
{
    private String $req = StudentRequest::class;
    public function __construct(
        private User $user, 
        private Student $student, 
        private Establishment $establishment, 
        private Speciality $speciality, 
        private Filiere $filiere,
        private Role $role
    ){}

    
    public function register(InitialRegisterRequest $request)
    {
        $hash = $this->user::generateHash();
        $user = null;
        DB::transaction(function () use ($request, &$user, $hash) {
            $role = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES['STUDENT']);
            $user = $this->user::create([
                'email' => $request->email,
                'register_verification_hash' => $hash,
                'person_type' => $this->student::class,
            ]);
            $user->roles()->attach($role->id);
        });
        return $user;
    }

    public function completeRegister(Request $request): Student
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
        $person = $this->student::create($validatedData);
        return $person;
    }

    public function getReferences(): array
    {
        return [
            'establishments' => $this->establishment::all(),
            'filieres' => $this->filiere::all(),
            'specialities' => $this->speciality::where('type', Speciality::TYPES['STUDENT'])->get(),
        ];
    }

    public function create_student($email): User
    {
        $hash = $this->user::generateHash();
        $role = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES['STUDENT']);
        $user = $this->user::create([
            'email' => $email,
            'register_verification_hash' => $hash,
            'person_type' => $this->student::class,
        ]);
        $user->roles()->attach($role->id);
        return $user;
    }
}
