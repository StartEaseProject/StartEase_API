<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\EstablishmentInterface;
use App\Http\Resources\EstablishmentResource;
use Illuminate\Http\Response;

class EstablishmentController extends BaseController
{
    public function __construct(
        private EstablishmentInterface $establishmentRepository
    ){}


    public function index()
    {
        $response = $this->establishmentRepository->all();
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'establishments' => EstablishmentResource::collection($response['establishments'])
            ]);
    }
}
