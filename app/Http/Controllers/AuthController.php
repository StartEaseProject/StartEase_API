<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\AuthRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function authenticate(AuthRequest $request)
    {   
        $user = User::firstWhere('email',$request->email);
        if(!$user){
            return $this->sendError('Wrong credentiels', Response::HTTP_UNAUTHORIZED);
        }

        if(!Hash::check($request->password, $user->password)){
            return $this->sendError('Wrong credentiels', Response::HTTP_UNAUTHORIZED);
        }

        if(!$user->is_enabled){
            return $this->sendError('Your account is disabled. Please contact the admin', Response::HTTP_UNAUTHORIZED);
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;
        $result = [
            'user' => new UserResource($user),
            'token' => $token
        ];
        return $this->sendResponse('Login succesfull',$result);
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
        return $this->sendResponse('Logged out succesfully');
    }

    public function getAuth()
    {   
        return $this->sendResponse('Auth retreived successfully', [
            'user' => new UserResource(auth()->user())
        ]);
    }
}
