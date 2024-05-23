<?php

namespace App\Http\Requests\Deliberation;

use App\Http\Requests\BaseRequest;

class CreateDeliberationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'data' => 'required|array|min:1',
            'data.*.member_id' => 'required|integer',
            'data.*.mark' => 'required|numeric',
            'data.*.mention' => 'required|string',
            'data.*.appreciation' => 'required|string',
            'reserves' => 'nullable|string',
        ];
    }

    /**
     * Get the data from the request.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->input('data', []);
    }
}
