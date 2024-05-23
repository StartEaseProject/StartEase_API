<?php

namespace App\Http\Requests\User\Update;

use App\Http\Requests\BaseRequest;

class VerifyPhoneRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'verif_code' => 'required|string|digits:6',
        ];
    }
}
