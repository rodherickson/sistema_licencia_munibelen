<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Rules\ImageValidation;

class Propietario extends FormRequest
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
            'nombre' => 'required|string|min:2|max:255',
            'apellidos' => 'required|string|min:2|max:255',
            'dni'=> 'required|string|max:8',
            'celular' => 'required|string|min:2|max:255',
            'correo' => 'required|email|min:2|max:255',
            'direccion' => 'required|string|min:2|max:255',
            'distrito' => 'required|string|min:2|max:255',
            'fotoVendedor.*' => [
                'required',
                'mimes:jpg,jpeg,png,gif',
                'min:1',
                new ImageValidation,
            ],
        ];
    }

    public function messages(): array
    {
        return[
            'nombre.required' => 'Debe ingresar un nombre :(',
            'nombre.string' => 'Formato de nombre Incorrecto :(',
            'nombre.min' => 'El nombre es demasiado corto :(',
            'nombre.max' => 'El nombre es demasiado Largo :(',
            'apellidos.required' => 'Debe ingresar su apellido :(',
            'apellidos.string' => 'Su apellido debe tener un formato correcto',
            'dni.required' => 'Por favor ingrese su DNI',
            'dni.max' => 'su DNI debe tener 8 digitos',
            'correo.required' => 'Por favor ingrese su correo electrónico',
            'celular.required' => 'Por favor ingrese su celular',
            'correo.email' =>  'El correo electrónico no tiene un formato valido',
            'correo.max' => 'El correo electrónico no tiene un formato valido',
            'direccion.required' => 'Por favor ingrese direccion',
            'distrito.required' => 'Por favor ingrese el distrito',
        ];
    }

}
