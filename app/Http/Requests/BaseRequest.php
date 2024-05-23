<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;


class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //put this as true in all requests
    }

    /**
     * Get the validation rules that apply to the request.

     */
    public function rules(): array
    {
        return [];
    }

    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            new JsonResponse(
                [
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            )
        ); 
    }


    public function messages() : array
    {
        return [
            'email.exists' => 'No user found with specified email',
        ];
    }
}
