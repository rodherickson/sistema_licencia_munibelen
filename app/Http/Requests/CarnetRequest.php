<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
<<<<<<< HEAD
use Illuminate\Validation\Rule;
use App\Rules\ImageValidation;
=======

>>>>>>> 81c481cc1c815c46bce2c08622903c5433bbf141
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
            'ubicacion' => 'required|string|max:255',
            'cuadra' => 'required|numeric',
            'largo' => 'required|numeric',
            'ancho' => 'required|numeric',
            'n_mesa' => 'required|numeric',
            'categoria' => 'required|string',
            'files.*' => [
                'required',
                'mimes:doc,docx,pdf,jpg,jpeg,png,gif',
                'min:1',
               new ImageValidation
            ],
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
            'files.*.required' => 'Debe subir por lo menos un archivo',
            'files.*.mimes' => 'Formato no permitido. Solo se acepta  tipo: doc,docx,pdf,jpg,img,jfif,webp,jpeg',
            'files.*.dimensions' => 'Dimensiones de la imagen inválidas',
            'files.*.max' => 'archivo muy pesado',
            'fecha_emision.required' => 'Debe ingresar una fecha de emision :(',
            'fecha_caducidad.required' => 'Debe ingresar una fecha de caducidad :(',
        ];
    }

}
