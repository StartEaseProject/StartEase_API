<?php

namespace App\Http\Requests\Announcement;

use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnnouncementRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'type' => ['required', Rule::in(array_values(Announcement::TYPES))],
            'date' => 'required_if:type,'. Announcement::TYPES['SINGLE_DAY'] .'|nullable|date',
            'start_date' => 'required_if:type,'. Announcement::TYPES['PERIOD'] .'|nullable|date',
            'end_date' => 'required_if:type,' . Announcement::TYPES['PERIOD'] . '|nullable|date',
            'visibility' => ['required', Rule::in(array_values(Announcement::VISIBILITY))],
        ];

        if (($this->input('type')===Announcement::TYPES['PERIOD']) && $this->input('start_date') && $this->input('end_date')) {
            $rules['start_date'] =  $rules['start_date'] . '|after:today';
            $rules['end_date'] = $rules['end_date'] . '|after:start_date';
        }

        return $rules;
    }
}
