<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Rules\passwordVerify;

class RegisterUserRequest extends FormRequest
{
    protected $stopOnFirstFailure=true;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'name' => 'required|string|min:2|max:255',
            'lastName' => 'required|string|min:2|max:255',
            'dni' => 'required|string|max:8',
            'email' => 'required|email|unique:users,email', 
            'password' =>  ['required', new passwordVerify],
            'type_user' => 'required|string|max:255',

        ];
    }

    public function messages(): array
    {
        return[
            'name.required' => 'Debe ingresar un nombre :(',
            'name.string' => 'Formato de nombre Incorrecto :(',
            'name.min' => 'El nombre es demasiado corto :(',
            'name.max' => 'El nombre es demasiado Largo :(',
            'lastName.required' => 'Debe ingresar su apellido :(',
            'lastName.string' => 'Su apellido debe tener un formato correcto',
            'email.required' => 'Por favor ingrese su correo electrónico',
            'email.email' =>  'El correo electrónico no tiene un formato valido',
            'email.max' => 'El correo electrónico non tiene un formato valido',
            'password.required' => 'Por favor ingrese contraseña',
            'type_user.required' => 'Por favor ingrese Tipo de usuario',
        ];
    }

}
