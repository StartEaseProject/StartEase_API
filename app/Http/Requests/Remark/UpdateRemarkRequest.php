<?php

namespace App\Http\Requests\Remark;

use App\Http\Requests\BaseRequest;

class UpdateRemarkRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'content' =>'required|string'
        ];
    }
}
