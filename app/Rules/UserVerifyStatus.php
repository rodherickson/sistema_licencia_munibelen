<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UserVerifyStatus implements ValidationRule
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
        ->where ( 'status','=',1)
        ->get();

        if(isset($user)){
            $fail('Su cuenta esta restringida :(');
        }
    }
}
