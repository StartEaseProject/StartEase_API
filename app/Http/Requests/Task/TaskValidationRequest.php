<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\BaseRequest;

class TaskValidationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'validated' => 'required|boolean',
            'refusal_motif' => 'required_if:validated,false|string',
        ];
    }
}
