<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\FiliereInterface;
use App\Http\Resources\FiliereResource;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FiliereController extends BaseController
{
    public function __construct(
        private FiliereInterface $filiereRepository
    ){}

    
    public function index()
    {
        $response = $this->filiereRepository->all();
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'filieres' => FiliereResource::collection($response['filieres'])
            ]);
    }
}
