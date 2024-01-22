<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;


class ChangePasswordRequest extends FormRequest
{
    protected $stopOnFirstFailure=true;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }


    protected function failedValidation(Validator $validator)
    {
     $jsonResponse=new JsonResponse([
        'status'=>'error',
        'messsage'=>messageValidation($validator)
     ],422);
     throw new HttpResponseException($jsonResponse);   
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idUser' => '',
            'newPassword' => ''
        ];
    }

    public function messages()
    {
        return[
            'idUser' => '',
            'newPassword' => ''
        ];
    }
}
