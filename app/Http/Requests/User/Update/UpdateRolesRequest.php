<?php

namespace App\Http\Requests\User\Update;

use App\Http\Requests\BaseRequest;

class UpdateRolesRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'user' => 'required|integer|exists:users,id',
            'roles' => 'array',
            'roles.*' => 'integer|exists:roles,id'
        ];
    }
}
