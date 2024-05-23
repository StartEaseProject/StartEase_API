<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ResetPassword\FinalResetRequest;
use App\Http\Requests\User\ResetPassword\SendResetCodeRequest;
use App\Http\Requests\User\ResetPassword\VerifyResetCodeRequest;
use App\Models\ResetPasswordToken;
use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends BaseController
{   
    public function sendCode(SendResetCodeRequest $request)
    {   
        $user = User::firstWhere('email', $request->email);
        if(!$user) return $this->sendError("No user found with specified email", Response::HTTP_NOT_FOUND);

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        ResetPasswordToken::updateOrCreate(
            ['email' => $user->email],
            ['code' => $code]
        );
        $user->notify(new PasswordResetCodeNotification($code));
        return $this->sendResponse("Code sent. Please check your email");
    }

    public function verifyCode(VerifyResetCodeRequest $request)
    {      
        $token = ResetPasswordToken::firstWhere('email', $request->email);
        if(!$token) return $this->sendError("No reset code found", Response::HTTP_NOT_FOUND);

        if($token->is_expired()) 
            return $this->sendError("The code has expired. Generate a new one", Response::HTTP_FORBIDDEN);

        if(!Hash::check($request->code, $token->code)) 
            return $this->sendError("The code you provided is invalid", Response::HTTP_FORBIDDEN);

        return $this->sendResponse("The code you provided is valid");
    }

    public function resetPassword(FinalResetRequest $request)
    {
        $token = ResetPasswordToken::firstWhere('email', $request->email);
        if(!$token) return $this->sendError("No reset code found", Response::HTTP_NOT_FOUND);

        if($token->is_expired()) 
            return $this->sendError("The code has expired. Generate a new one", Response::HTTP_FORBIDDEN);

        if(!Hash::check($request->code, $token->code)) 
            return $this->sendError("The code you provided is invalid", Response::HTTP_FORBIDDEN);

        $token->delete();
        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);
        
        return $this->sendResponse("Your password was reset successfully");
    }
}
