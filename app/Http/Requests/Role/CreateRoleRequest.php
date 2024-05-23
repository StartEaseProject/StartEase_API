<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseRequest;

class CreateRoleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:roles|max:255',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }
}