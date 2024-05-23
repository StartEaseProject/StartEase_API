<?php

namespace App\Http\Requests\User\Update;

use App\Http\Requests\BaseRequest;

class UpdatePasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|max:20',
            'confirm_new_password' => 'required|string|same:new_password',
        ];
    }
}
