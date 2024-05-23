<?php

namespace App\Http\Requests\Permission;

use App\Http\Requests\BaseRequest;

class CreatePermissionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:permissions'
        ];
    }
}
