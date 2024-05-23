<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class AuthRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.

     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:8|max:20',
        ];
    }
}
