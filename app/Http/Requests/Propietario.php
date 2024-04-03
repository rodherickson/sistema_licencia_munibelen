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
            'nombre' => 'required|string|max:80',
            'apellidos' => 'required|string|max:80',
            'dni'=> 'required|string|max:8',
            'celular' => 'required|string|min:9|max:15',
            'correo' => 'required|email|max:255',
            'direccion' => 'required|string|max:50',
            'distrito' => 'required|string|max:20',
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
            // 'nombre.min' => 'El nombre es demasiado corto :(',
            'nombre.max' => 'El nombre es demasiado Largo :(',
            'apellidos.required' => 'Debe ingresar su apellido :(',
            'apellidos.string' => 'Su apellido debe tener un formato correcto',
            'apellidos.max' => 'Su apellido es demasiado largo',
            'dni.required' => 'Por favor ingrese su DNI',
            'dni.max' => 'su DNI debe tener 8 digitos',
            'correo.required' => 'Por favor ingrese su correo electrónico',
            'celular.required' => 'Por favor ingrese su celular',
            'celular.min' => ' El teléfono debe tener al menos 9 caracteres numéricos',
            'celular.max' => ' El teléfono debe tener como maximo 15 caracteres numéricos',
            'correo.email' =>  'El correo electrónico no tiene un formato valido',
            'correo.max' => 'El correo electrónico no tiene un formato valido',
            'direccion.required' => 'Por favor ingrese direccion',
            'direccion.max'=>'La dirección no puede tener más de 50 caracteres',
            'distrito.required' => 'Por favor ingrese el distrito',
            'distrito.max'=>'El distrito no puede tener más de 20 caracteres',
        ];
    }

}
