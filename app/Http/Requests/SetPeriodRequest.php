<?php

namespace App\Http\Requests;


class SetPeriodRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'period' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date|after:today',
        ];
    }
}
