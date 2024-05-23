<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;
use App\Models\Project;
use Illuminate\Validation\Rule;

class ChangeStatusRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $statuses = [
            Project::UI_STATUSES["REFUSED"],
            Project::UI_STATUSES["ACCEPTED"],
            Project::UI_STATUSES["RECOURSE"],
        ];
        return [
            'project' => 'required|integer|exists:projects,id',
            'status' => ['required', 'string', Rule::in($statuses)],
        ];
    }
}
