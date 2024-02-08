<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObtenerCarnetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dni' => 'required|numeric|digits:8',
        ];
    }

    public function messages(): array
    {
        return [
            'dni.required' => 'El DNI es requerido.',
            'dni.numeric' => 'El DNI debe ser numérico.',
            'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
        ];
    }

}
