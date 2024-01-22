<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class passwordVerify implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{5,}$/', $value)){
            $fail('La contraseña debe contener números, mayúsculas y tener una longitud mayor a 5 caracteres');
        }
    }
}
