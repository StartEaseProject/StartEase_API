<?php

namespace App\Http\Requests\Comment;

use App\Http\Requests\BaseRequest;

class ReplyCommentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'parent_id' => 'required',
        ];
    }
}
