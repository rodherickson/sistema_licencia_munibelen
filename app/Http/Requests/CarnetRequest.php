<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

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
            'idpropietario' => '',
            'idrubro' => '',
            'ubicacion' => 'required',
            'cuadra' => 'required',
            'largo' => 'required',
            'ancho' => 'required',
            'n_mesa' => 'required',
            'categoria' => 'required',
            'fecha_emision' => 'required',
            'fecha_caducidad' => '',
        ];
    }

    public function messages(): array
    {
        return[
            'idpropietario.required' => 'Debe ingresar un propietario :(',
            'idrubro.required' => 'Debe ingresar un rubro :(',
            'ubicacion.required' => 'Debe ingresar una ubicacion :(',
            'cuadra.required' => 'Debe ingresar una cuadra :(',
            'largo.required' => 'Debe ingresar una longitud de largo :(',
            'ancho.required' => 'Debe ingresar una longitud de ancho :(',
            'n_mesa.required' => 'Debe ingresar un numero de mesa :(',
            'categoria.required' => 'Debe ingresar una categoria :(',
            'fecha_emision.required' => 'Debe ingresar una fecha de emision :(',
            'fecha_caducidad.required' => 'Debe ingresar una fecha de caducidad :(',
        ];
    }

}
