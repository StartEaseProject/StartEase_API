<?php

namespace App\Http\Repositories;

use App\Http\Requests\User\CompleteRegister\InternshipRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\Establishment;
use App\Models\Grade;
use App\Models\Internship_service_member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InternshipRepository
{
    private String $req = InternshipRequest::class;
    public function __construct(
        private User $user,
        private Internship_service_member $internship,
        private Establishment $establishment,
        private Grade $grade,
        private Role $role
    ){}


    public function create(CreateUserRequest $request)
    {
        $user = new User();
        $hash = $this->user::generateHash();

        DB::transaction(function () use ($request, &$user, $hash) {
            $role = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES['INTERNSHIP']);
            $res = $this->internship::create([
                'establishment_id' => $request->establishment_id
            ]);
            $user = $this->user::create([
                'email' => $request->email,
                'register_verification_hash' => $hash,
                'person_type' => $this->internship::class,
                'person_id' => $res->id,
            ]);
            $user->roles()->attach($role->id);
        });
        return $user;
    }

    public function completeRegister(Request $request, $id): Internship_service_member
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
        $person = $this->internship::find($id);
        $person->fill([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'grade_id' => $validatedData['grade_id'],
        ]);
        $person->save();
        return $person;
    }

    public function getReferences(): array
    {
        return [
            'establishments' => $this->establishment::all(),
            'grades' => $this->grade::where('type', Grade::TYPES['INTERNSHIP_SERVICE_MEMBER'])->get()
        ];
    }
}
