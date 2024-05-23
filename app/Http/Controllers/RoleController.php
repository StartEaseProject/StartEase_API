<?php

namespace App\Http\Controllers;


use Illuminate\Http\Response;
use App\Http\Interfaces\RoleInterface;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;


class RoleController extends BaseController
{
    public function __construct(
        private RoleInterface $roleRepository
    ){}

    
    public function index()
    {
        $response = $this->roleRepository->all();
        return !$response['success'] ? 
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR):
            $this->sendResponse($response['message'], [
                'roles' => RoleResource::collection($response['roles'])
            ]);
    }

    public function show($id){
        $response = $this->roleRepository->getRole($id);
        return !$response['success'] ? 
            $this->sendError($response['message'], Response::HTTP_NOT_FOUND) :
            $this->sendResponse($response['message'], [
                'role' => new RoleResource($response['role'])
            ]);
    }

    public function store(CreateRoleRequest $request)
    {
        $response = $this->roleRepository->createRole($request);
        return !$response['success'] ? 
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR):
            $this->sendResponse($response['message'], [
                'role' => new RoleResource($response['role'])
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request)
    {
        $response = $this->roleRepository->updatePermissions($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) : 
            $this->sendResponse($response['message'],[
                'role' => new RoleResource($response['role'])
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $response = $this->roleRepository->destroyRole($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_NOT_FOUND) : 
            $this->sendResponse($response['message']);
    }
}