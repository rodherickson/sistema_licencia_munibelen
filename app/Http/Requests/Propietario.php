<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

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
            'nombre' => 'required|string|min:2|max:255',
            'apellido' => 'required|string|min:2|max:255',
            'dni' => 'required|string|max:8',
            'celular' => 'required|string|min:2|max:255',
            'correo' => 'required|email|min:2|max:255',
            'direccion' => 'required|string|min:2|max:255',
        ];
    }

    public function messages(): array
    {
        return[
            'nombre.required' => 'Debe ingresar un nombre :(',
            'nombre.string' => 'Formato de nombre Incorrecto :(',
            'nombre.min' => 'El nombre es demasiado corto :(',
            'nombre.max' => 'El nombre es demasiado Largo :(',
            'apellido.required' => 'Debe ingresar su apellido :(',
            'apellido.string' => 'Su apellido debe tener un formato correcto',
            'dni.required' => 'Por favor ingrese su DNI',
            'email.required' => 'Por favor ingrese su correo electrónico',
            'celular.required' => 'Por favor ingrese su celular',
            'correo.email' =>  'El correo electrónico no tiene un formato valido',
            'correo.max' => 'El correo electrónico no tiene un formato valido',
            'direccion.required' => 'Por favor ingrese contraseña',
        ];
    }

}
