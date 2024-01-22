<?php

use Illuminate\Validation\Validator;


function messageValidation(Validator $validator){

   $errors= $validator->errors()->getMessages();
   $content=array_values($errors);
   $message=$content[0][0];
   return $message;

}


?>

