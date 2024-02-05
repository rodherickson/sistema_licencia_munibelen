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
            
            'idtipo_multa' => 'required|numeric',
            'idarea' => 'required|numeric',
            'files.*' => 'required|mimes:doc,docx,pdf',
        ];
    }


    public function messages(): array
    {
        return[
            
            'idtipo_multa.required' => 'Debe ingresar un tipo multa :(',
            'idarea_multa.required' => 'Debe ingresar un area :(',
            'files.*.required' => 'Debe subir por lo menos un archivo',
            'files.*.mimes' => 'Formato no permitido. Solo se acepta  tipo: doc,docx,pdf,jpg,img,jfif,webp,jpeg',
            'expiredate.required' => 'Debe ingresar una fecha :(',
        ];
    }
}
