<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ImageValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $extension = $value->getClientOriginalExtension();
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (!$value->isValid()) {
               $fail('El archivo no es una imagen válida.');
            }
            $dimensions = getimagesize($value);
            $width = $dimensions[0];
            $height = $dimensions[1];
            
        $anchoImagenRecibida = $width;
        $altoImagenRecibida = $height;
        $proporcionImagenRecibida = $anchoImagenRecibida / $altoImagenRecibida;

        $anchoCarnet = 148;
        $altoCarnet = 184;
        $proporcionCarnet = $anchoCarnet / $altoCarnet;

        if ($anchoImagenRecibida >= $anchoCarnet && (round($proporcionCarnet, 2) === round($proporcionImagenRecibida, 2))) {
            
           
        } else {
            // La imagen no cumple con los estándares
            $fail("La imagen no cumple con los estándares de dimensiones. Se esperaba una proporción de 148x184.");

        }
        }
    }
}
