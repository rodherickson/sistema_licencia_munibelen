<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class LicenciaRequest extends FormRequest
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
            'idpropietario' => 'required|numeric',
            'nombreempresa' => 'required|string|max:255',
            'ruc' => 'required|numeric',
            'direccion' => 'required|string|max:255',
            'area' => 'required|numeric',
            'aforo' => 'required|numeric',
            'files.*' => 'required|mimes:doc,docx,pdf,jpg,jpeg,png,gif',
        ];
    }

    public function messages(): array
    {
        return[
            'idpropietario.required' => 'Debe ingresar un propietario :(',
            'nombreempresa.required' => 'Debe ingresar un nombre de la empresa :(',
            'ruc.required' => 'Debe ingresar Su ruc :(',
            'direccion.required' => 'Debe ingresar una direccion :(',
            'area.required' => 'Debe ingresar una longitud de area :(',
            'aforo.required' => 'Debe ingresar un numero de aforo :(',
            'files.required' => 'Debe subir por lo menos un archivo',
            'files.*.mimes' => 'Formato no permitido. Solo se acepta  tipo: doc,docx,pdf,jpg,img,jfif,webp,jpeg',
            
        ];
    }

}
