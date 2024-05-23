<?php

namespace App\Http\Repositories;

use App\Http\Requests\User\CompleteRegister\CommitteeRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\Establishment;
use App\Models\Grade;
use App\Models\Role;
use App\Models\Scientific_committee_member;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommitteeRepository
{
    private String $req = CommitteeRequest::class;
    public function __construct(
        private User $user,
        private Scientific_committee_member $committee,
        private Establishment $estblishment,
        private Speciality $speciality,
        private Grade $grade,
        private Role $role
    ){}

    
    public function create(CreateUserRequest $request)
    {
        $user = null;
        $hash = $this->user::generateHash();

        DB::transaction(function () use ($request, &$user, $hash) {
            $role = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES['COMMITTEE']);
            $res = $this->committee::create([
                'establishment_id' => $request->establishment_id
            ]);
            $user = $this->user::create([
                'email' => $request->email,
                'register_verification_hash' => $hash,
                'person_type' => $this->committee::class,
                'person_id' => $res->id,
            ]);
            $user->roles()->attach($role->id);
        });
        return $user;
    }

    public function createPresident(CreateUserRequest $request)
    {
        $user = null;
        $hash = $this->user::generateHash();

        DB::transaction(function () use ($request, &$user, $hash) {
            $roles = $this->role::whereIn('name', [$this->role::DEFAULT_ROLES['COMMITTEE'], $this->role::DEFAULT_ROLES['INCUBATOR_PRESIDENT']])->get()->pluck('id');
            $res = $this->committee::create([
                'establishment_id' => $request->establishment_id
            ]);
            $user = $this->user::create([
                'email' => $request->email,
                'register_verification_hash' => $hash,
                'person_type' => $this->committee::class,
                'person_id' => $res->id,
            ]);
            $user->roles()->attach($roles);
        });
        return $user;
    }

    public function completeRegister(Request $request, $id): Scientific_committee_member
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
        $person = $this->committee::find($id);
        $person->fill([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'speciality_id' => $validatedData['speciality_id'],
            'grade_id' => $validatedData['grade_id'],
        ]);
        $person->save();
        return $person;
    }

    public function getReferences(): array
    {
        return [
            'establishments' => $this->estblishment::all(),
            'grades' => $this->grade::all(),
            'specialities' => $this->speciality::where('type', Speciality::TYPES['TEACHER'])->get(),
        ];
    }
}
