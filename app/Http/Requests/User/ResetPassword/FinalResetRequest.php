<?php

namespace App\Http\Requests;

namespace App\Http\Requests\User\ResetPassword;
use App\Http\Requests\BaseRequest;

class FinalResetRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'code' => 'required|string|digits:6',
            'password' => 'required|string|min:8',
            'confirm_password'=> 'required|string|same:password'
        ];
    }
}
