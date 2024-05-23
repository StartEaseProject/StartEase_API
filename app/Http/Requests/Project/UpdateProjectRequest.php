<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;
use App\Models\Project;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProjectRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_values(Project::TYPES))],
            'trademark_name' => 'required|string|max:255',
            'scientific_name' => 'required|string|max:255',
            'resume' => 'required|string',
            'supervisor' => 'nullable|email|different:co_supervisor',
            'co_supervisor' => 'nullable|email',
            'members' => 'array|min:0|max:6',
            'members.*' => 'email|distinct',
            'old_files' => 'array',
            'files' => 'array',
            'files.*' => 'file|mimes:pdf',
            'files_types' => 'array',
            'files_types.*' => ['required', 'distinct', Rule::in(array_values(Project::FILES_TYPES['COMMITTEE']))],
        ];
    }
}
