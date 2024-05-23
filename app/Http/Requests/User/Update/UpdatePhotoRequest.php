<?php

namespace App\Http\Requests\User\Update;

use App\Http\Requests\BaseRequest;

class UpdatePhotoRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }
}
