<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseRequest;

class UpdateRoleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'role' => 'required|integer|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id'
        ];
    }
}
