<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\RegisterInterface;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\InitialRegisterRequest;
use App\Http\Requests\User\Update\UpdatePhoneRequest;
use App\Http\Requests\User\Update\VerifyPhoneRequest;
use App\Http\Resources\EstablishmentResource;
use App\Http\Resources\FiliereResource;
use App\Http\Resources\GradeResource;
use App\Http\Resources\SpecialityResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RegisterController extends BaseController
{
    public function __construct(
        private RegisterInterface $registerRepository
    ){}
    
    
    public function store(CreateUserRequest $request)
    {
        $response = $this->registerRepository->createUser($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) : 
            $this->sendResponse($response['message'],[
                'user' => new UserResource($response['user'])
            ]);
    }

    public function initialRegister(InitialRegisterRequest $request)
    {
        $response = $this->registerRepository->initialRegister($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) : 
            $this->sendResponse($response['message'],[
                'user' => new UserResource($response['user'])
            ]);
    }

    public function verifyToken($payload)
    {
        $response = $this->registerRepository->verifyHash($payload);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) : 
            $this->sendResponse($response['message'], [
                'user'=> new UserResource($response['user']),
                'establishments' => isset($response['establishments']) ? EstablishmentResource::collection($response['establishments']) : null,
                'grades' => isset($response['grades']) ? GradeResource::collection($response['grades']) : null,
                'filieres' => isset($response['filieres']) ? FiliereResource::collection($response['filieres']) : null,
                'specialities' => isset($response['specialities']) ? SpecialityResource::collection($response['specialities']) : null
            ]);
    }

    public function completeRegister(Request $request, $payload)
    {
        $response = $this->registerRepository->completeRegister($request,$payload);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) : 
            $this->sendResponse($response['message'],[
                'user' => new UserResource($response['user']),
            ]);
    }

    public function setPhoneNumber(UpdatePhoneRequest$request, $payload)
    {
        $response = $this->registerRepository->sendVerificationCode($request, $payload);
        return !$response['success'] ?
        $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message']);
    }

    public function verifyPhoneNumber(VerifyPhoneRequest$request, $payload)
    {
        $response = $this->registerRepository->setPhoneNumber($request, $payload);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message'], [
                'user' => new UserResource($response['user'])
            ]);
    }
}
