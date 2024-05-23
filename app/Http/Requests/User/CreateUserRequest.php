<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class CreateUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $possible = [
            User::TYPES['HEADMASTER'], 
            User::TYPES['COMMITTEE'], 
            User::TYPES['INTERNSHIP'],
            User::TYPES['INCUBATOR_PRESIDENT']
        ];
        return [
            'email' => 'required|email|unique:users,email',
            'person_type' => ['required', Rule::in($possible)],
            'establishment_id' => 'required|integer|exists:establishments,id'
        ];
    }
}
