<?php

namespace App\Http\Requests\User\CompleteRegister;

use App\Http\Requests\BaseRequest;

class TeacherRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'matricule' => 'required|string|max:255',
            'birthday' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'establishment_id' => 'required|integer|exists:establishments,id',
            'speciality_id' => 'required|integer|exists:specialities,id',
            'grade_id' => 'required|integer|exists:grades,id',

            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|max:255',
            'confirm_password' => 'required|same:password',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
