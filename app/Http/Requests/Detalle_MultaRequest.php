<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class Detalle_MultaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    protected function failedValidation(Validator $validator)
    {
     $jsonResponse=new JsonResponse([
        'success'=>false,
        'message'=>messageValidation($validator)
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
            'fecha' => 'required|date',
            'status' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return[
            'fecha.required' => 'Debe ingresar una fecha :(',
            'status.required' => 'Debe ingresar un estado :(',
         
        ];
    }
}
