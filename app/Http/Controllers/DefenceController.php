<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\DefenceInterface;
use App\Http\Requests\Defence\CreateDefenseRequset;
use App\Http\Requests\Defence\UpdateDefenceRequest;
use App\Http\Requests\Defence\UploadThesisDefFilesRequest;
use App\Http\Resources\DefenceResource;
use Illuminate\Http\Response;

class DefenceController extends BaseController
{
    public function __construct(
        private DefenceInterface $defenceRepository
    ) {}


    public function index()
    {
        $response = $this->defenceRepository->all();
        return !$response['success'] ?
        $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'defences' => DefenceResource::collection($response['defences'])
            ]);
    }

    public function show($id)
    {
        $response = $this->defenceRepository->getById($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message'], [
                'defence' => new DefenceResource($response['defence'])
            ]);
    }

    public function store(CreateDefenseRequset $request, $project_id)
    {
        $response = $this->defenceRepository->create_defence($request, $project_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'defence' => new DefenceResource($response['defence'])
            ]);
    }

    public function update(UpdateDefenceRequest $request, $id)
    {
        $response = $this->defenceRepository->update_defence($request, $id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'defence' => new DefenceResource($response['defence'])
            ]);
    }

    public function update_files(UploadThesisDefFilesRequest $request, $id)
    {
        $response = $this->defenceRepository->uploadProjectThesisDefFiles($request, $id);
        return !$response['success'] ?
        $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'defence' => new DefenceResource($response['defence'])
            ]);
    }

    public function destroy($id)
    {
        $response = $this->defenceRepository->delete($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'defence' => new DefenceResource($response['defence'])
            ]);
    }
}
