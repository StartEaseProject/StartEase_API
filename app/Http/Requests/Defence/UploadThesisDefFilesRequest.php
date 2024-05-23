<?php

namespace App\Http\Requests\Defence;

use App\Http\Requests\BaseRequest;
use App\Models\Defence;
use Illuminate\Validation\Rule;

class UploadThesisDefFilesRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $possible = [
            Defence::FILES_TYPES['MEMORY'],
            Defence::FILES_TYPES['BMC'],
            Defence::FILES_TYPES['LABEL-BREVET'],
        ];
        
        return [
            'old_files' => 'array',
            'files' => 'array|min:1',
            'files.*' => 'file|mimes:pdf',
            'files_types' => 'array|min:1',
            'files_types.*' => ['distinct', Rule::in($possible)],
        ];
    }

}
