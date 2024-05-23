<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\GradeInterface;
use App\Http\Resources\GradeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GradeController extends BaseController
{
    public function __construct(
        private GradeInterface $gradeRepository
    ){}

    
    public function index()
    {
        $response = $this->gradeRepository->all();
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'grades' => GradeResource::collection($response['grades'])
            ]);
    }
}
