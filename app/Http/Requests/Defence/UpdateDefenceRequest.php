<?php

namespace App\Http\Requests\Defence;

use Carbon\Carbon;
use App\Models\Defence;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDefenceRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $oneYearFromNow = Carbon::today()->addYear()->toDateTimeString();
        return [
            'date' => ['required', 'date', 'after:today', 'before:' . $oneYearFromNow],
            'time' => ['required', 'date_format:H:i'],
            'room_id' => 'nullable|exists:rooms,id',
            'other_place' => 'nullable|string',
            'mode' => ['required', Rule::in(array_values(Defence::MODES))],
            'nature' => ['required', Rule::in(array_values(Defence::NATURES))],
            'president' => 'required|email|different:guest',
            'examiners' => 'required|array|min:2|max:3|distinct',
            'examiners.*' => 'required|email',
            'guest' => 'nullable|string',
        ];
    }
}
