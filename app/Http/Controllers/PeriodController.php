<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\PeriodInterface;
use App\Http\Requests\SetPeriodRequest;
use App\Http\Resources\PeriodResource;
use Illuminate\Http\Response;

class PeriodController extends BaseController
{
    public function __construct(
        private PeriodInterface $periodRepository
    ){}

    
    public function index_establishment()
    {
        $response = $this->periodRepository->getByEstablishment();
        return !$response['success'] ?
        $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'periods' => PeriodResource::collection($response['periods'])
            ]);
    }

    public function update(SetPeriodRequest $request)
    {
        $response = $this->periodRepository->setPeriod($request);
        return !$response['success'] ?
        $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message'], [
                'period' => new PeriodResource($response['period'])
            ]);
    }
}
