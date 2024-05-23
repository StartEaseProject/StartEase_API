<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\DeliberationInterface;
use App\Http\Requests\Deliberation\CreateDeliberationRequest;
use App\Http\Resources\DeliberationResource;
use Illuminate\Http\Response;
class DeliberationController extends BaseController
{
    public function __construct(
        private DeliberationInterface $deliberationRepository
    ){}
    

    public function store(CreateDeliberationRequest $request, $defence_id)
    {
        $response = $this->deliberationRepository->create($request, $defence_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message'], [
                'deliberation' => new DeliberationResource($response['deliberation'])
            ]);
    }

    public function show($defence_id){
        $response = $this->deliberationRepository->getByDefence($defence_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message'], [
                'deliberation' => new DeliberationResource($response['deliberation'])
            ]);
    }
}
