<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Rules\EmailVerifyRule;

class LoginRequest extends FormRequest
{
    protected $stopOnFirstFailure=true;
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
            'email' => ['required','email', 'max:255', new EmailVerifyRule],
            'password' => 'required|max:255'
        ];
    }

    public function messages(): array
    {
        return[
            'email.required' => 'Por favor ingrese su correo electrónico',
            'email.email' =>  'El correo electrónico no tiene un formato valido',
            'email.max' => 'El correo electrónico non tiene un formato valido',
            'password.required' => 'Por favor ingrese contraseña',
            'password.max' => 'La contrasena excede el limite permitido de caracteres',
        ];
    }




}
