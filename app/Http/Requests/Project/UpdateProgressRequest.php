<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;

class UpdateProgressRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'progress' => ['required', 'numeric', 'between:1,100'],
            'observation' => ['nullable', 'string'],
        ];
    }
}
