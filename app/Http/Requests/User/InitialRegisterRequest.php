<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Validation\Rule;

class InitialRegisterRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $possible = [
            User::MODELSTOTYPES[Student::class],
            User::MODELSTOTYPES[Teacher::class],
        ];
        return [
            'email' => 'required|email|unique:users,email',
            'person_type' => ['required', Rule::in($possible)]
        ];
    }
}
