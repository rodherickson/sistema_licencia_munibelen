<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class EmailVerifyRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        
        $user=DB::table('users')
        ->where('email','=',$value)
        ->whereNull('email_verified_at')
        ->exists();

        if(!$user){
            $fail('Cuenta de usuario no verificada');
        }

    }
}
