<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\BaseRequest;

class SubmitTaskRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'submission_description' =>'required|string',
            'resources' => 'array',
            'names' => 'array',
            'names.*' => 'required|string',
            'resources.*' => 'file|mimes:pdf',
        ];
    }
}
