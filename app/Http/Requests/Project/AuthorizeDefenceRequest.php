<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;

class AuthorizeDefenceRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'file|mimes:pdf'
        ];
    }
}
