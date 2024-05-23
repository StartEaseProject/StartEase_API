<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\RemarkInterface;
use App\Http\Requests\Remark\CreateRemarkRequest;
use App\Http\Requests\Remark\UpdateRemarkRequest;
use App\Http\Resources\RemarkResource;
use Illuminate\Http\Response;

class RemarkController extends BaseController
{
    public function __construct(
        private RemarkInterface $remarkRepository
    ){}

    
    public function projectRemarks($id)
    {
        $response = $this->remarkRepository->get_by_projectId($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'remarks' => RemarkResource::collection($response['remarks'])
            ]);
    }

    public function destroy($id)
    {
        $response = $this->remarkRepository->destroy($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message']);
    }

    public function update(UpdateRemarkRequest $request)
    {
        $response = $this->remarkRepository->update($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'remark' => new RemarkResource($response['remark'])
            ]);
    }

    public function store(CreateRemarkRequest $request)
    {
        $response = $this->remarkRepository->create($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'remark' => new RemarkResource($response['remark'])
            ]);
    }
}
