<?php

namespace App\Http\Requests\Remark;

use App\Http\Requests\BaseRequest;

class CreateRemarkRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => 'integer | required',
            'content' => 'string | required'
        ];
    }
}
