<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\BaseRequest;

class CreateTaskRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' =>'required|string|max:255',
            'description' =>'required|string',
            'deadline' => 'required|date|after_or_equal:today',
            'resources' =>'required|array|min:1',
            'resources.*' => 'required|string|max:255',
        ];
    }
}
