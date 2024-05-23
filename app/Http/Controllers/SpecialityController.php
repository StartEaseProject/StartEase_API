<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\SpecialityInterface;
use App\Http\Resources\SpecialityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpecialityController extends BaseController
{
    public function __construct(
        private SpecialityInterface $specialityRepository
    ){}

    
    public function index()
    {
        $response = $this->specialityRepository->all();
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'specialities' => SpecialityResource::collection($response['specialities'])
            ]);
    }
}
