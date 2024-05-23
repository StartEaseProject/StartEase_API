<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\UserInterface;
use App\Http\Requests\User\Update\UpdateRolesRequest;
use App\Http\Requests\User\Update\UpdatePasswordRequest;
use App\Http\Requests\User\Update\UpdatePhoneRequest;
use App\Http\Requests\User\Update\UpdatePhotoRequest;
use App\Http\Requests\User\Update\VerifyPhoneRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Response;


class UserController extends BaseController
{
    public function __construct(
        private UserInterface $userRepository
    ){}

    
    public function index()
    {
        $response = $this->userRepository->all();
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'users' => UserResource::collection($response['users'])
            ]);
    }

    public function show($id)
    {
        $response = $this->userRepository->getById($id);
        return !$response['success'] ? 
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'user' => new UserResource($response['user'])
            ]);
    }

    public function getRoles($id)
    {
        $response = $this->userRepository->rolesList($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_NOT_FOUND) :
            $this->sendResponse($response['message'], [
                'roles' => RoleResource::collection($response['roles'])
            ]);
    }

    public function enable($id)
    {
        $response = $this->userRepository->enableUser($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message'], [
                'user' => new UserResource($response['user'])
            ]);
    }

    public function disable($id)
    {
        $response = $this->userRepository->disableUser($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message'], [
                'user' => new UserResource($response['user'])
            ]);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $response = $this->userRepository->updateAuthPassword($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message']);
    }

    public function updatePhoto(UpdatePhotoRequest $request)
    {
        $response = $this->userRepository->updateAuthPhoto($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'user' => new UserResource($response['user'])
            ]);
    }

    public function updatePhoneNumber(UpdatePhoneRequest $request)
    {
        $response = $this->userRepository->sendVerificationCode($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message']);
    }

    public function verifyPhoneNumber(VerifyPhoneRequest $request)
    {
        $response = $this->userRepository->updateAuthPhoneNumber($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message'], [
                'user' => new UserResource($response['user'])
            ]);
    }

    public function updateRoles(UpdateRolesRequest $request)
    {
        $response = $this->userRepository->updateUserRoles($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'user' => new UserResource($response['user'])
            ]);
    }
}
