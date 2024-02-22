<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class MultaRequest extends FormRequest
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
            
            'idtipoMulta' => 'required|numeric',
            'anexosAdjuntos.*' => 'required|mimes:doc,docx,pdf,jpg,jpeg,png,gif',
        ];
    }


    public function messages(): array
    {
        return[
            
            'idtipo_multa.required' => 'Debe ingresar un tipo multa :(',
            'anexosAdjuntos.required' => 'Debe subir por lo menos un archivo',
            'anexosAdjuntos.mimes' => 'Formato no permitido. Solo se acepta  tipo: doc,docx,pdf,jpg,img,jfif,webp,jpeg',
            'expiredate.required' => 'Debe ingresar una fecha :(',
        ];
    }
}
