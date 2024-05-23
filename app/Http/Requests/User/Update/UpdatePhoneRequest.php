<?php

namespace App\Http\Requests\User\Update;

use App\Http\Requests\BaseRequest;

class UpdatePhoneRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required|unique:users,phone_number|regex:/^[+]?[0-9]{3,}$/',
        ];
    }
}
