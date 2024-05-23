<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Rest of controllers will extend this class instead 
class BaseController extends Controller
{   
    public function sendResponse($message, $data=null, $code=Response::HTTP_OK)
    {
        $response = [
            "success" => true,
            "message" => $message,
            "data" => $data
        ];
        return response()->json($response, $code);
    }

    public function sendError($message, $code ,$errors=null)
    {
        $response = [
            "success" => false,
            "message" => $message,
            "errors" => $errors
        ];
        return response()->json($response, $code);
    }
}
