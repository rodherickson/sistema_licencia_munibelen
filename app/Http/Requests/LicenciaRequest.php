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
           
            'razonSocial' => 'required|string|max:255',
            'nombreComercial' => 'required|string|max:255|unique:nombrescomerciales,nombreComercial',
            'ruc' => 'required|numeric',
            'direccionEstablecimiento' => 'required|string|max:255',
            'distritoEstablecimiento' => 'required|string|max:255',
            'area' => 'required|numeric',
            'aforo' => 'required|numeric',
            'vigencia' => 'required|string',
            'inspector' => 'required|string|max:255',
            'anexosAdjuntos.*' => 'required|mimes:doc,docx,pdf,jpg,jpeg,png,gif',
        ];
    }

    public function messages(): array
    {
        return[
            'razonSocial.required' => 'Debe ingresar un nombre de la razon Social :(',
            'ruc.required' => 'Debe ingresar Su ruc :(',
            'ruc.numeric' => 'Debe ingresar datos numericos en el campo ruc :(',
            'nombreComercial.required' => 'Debe ingresar una nombre Comercial :(',
            'nombreComercial.unique' => 'El nombre Comercial ya existe :(',
            'direccionEstablecimiento.required' => 'Debe ingresar Dirección Del Establecimiento :(',
            'distritoEstablecimiento.required' => 'Debe ingresar Distrito Del Establecimiento :(',
            'area.required' => 'Debe ingresar una longitud de area :(',
            'inspector.required' => 'Debe ingresar inspector :(',
            'vigencia.required' => 'Debe ingresar el dato de vigencia :(',
            'vigencia.string' => 'Debe ingresar un dato valido en el campo vigencia :(',
            'area.numeric' => 'Debe ingresar una cantidad numerica en el campo area :(',
            'aforo.required' => 'Debe ingresar un numero de aforo :(',
            'aforo.numeric' => 'Debe ingresar una cantidad numerica en el campo aforo :(',
            'files.*.required' => 'Debe subir por lo menos un archivo',
            'files.*.mimes' => 'Formato no permitido. Solo se acepta  tipo: doc,docx,pdf,jpg,img,jfif,webp,jpeg',
            
        ];
    }

}
