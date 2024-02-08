<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Rules\ImageValidation;
class CarnetRequest extends FormRequest
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
            'idrubro' => 'required|numeric',
            'lugarEstablecimiento' => 'required|string|max:255',
            'cuadra' => 'required|numeric',
            'largo' => 'required|numeric',
            'ancho' => 'required|numeric',
            'nroMesa' => 'required|numeric',
            'categoria' => 'required|string',
            'fotoVendedor.*' => [
                'required',
                'mimes:jpg,jpeg,png,gif',
                'min:1',
               new ImageValidation
            ],

            'anexosAdjuntos*' => [
                'required',
                'mimes:doc,docx,pdf',
                'min:1',
            ],
            
        ];
    }

    public function messages(): array
    {
        return[
            'idpropietario.required' => 'Debe ingresar un propietario :(',
            'idrubro.required' => 'Debe ingresar un rubro :(',
            'lugarEstablecimiento.required' => 'Debe ingresar un lugarEstablecimiento :(',
            'cuadra.required' => 'Debe ingresar una cuadra :(',
            'largo.required' => 'Debe ingresar una longitud de largo :(',
            'ancho.required' => 'Debe ingresar una longitud de ancho :(',
            'nroMesa.required' => 'Debe ingresar un numero de mesa :(',
            'categoria.required' => 'Debe ingresar una categoria :(',
            'files.*.required' => 'Debe subir por lo menos un archivo',
            'files.*.mimes' => 'Formato no permitido. Solo se acepta  tipo: doc,docx,pdf,jpg,img,jfif,webp,jpeg',
            'files.*.dimensions' => 'Dimensiones de la imagen inválidas',
            'files.*.max' => 'archivo muy pesado',
            'fechaEmision.required' => 'Debe ingresar una fecha de emision :(',
            'fechaCaducidad.required' => 'Debe ingresar una fecha de caducidad :(',
        ];
    }

}
