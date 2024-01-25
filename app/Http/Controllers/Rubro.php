<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rubro as rub;

class Rubro extends Controller
{
    public function listRubro(){

        try{

            $rubros=rub::listRubro();
            return response()->json(['status'=>'success', 'data' => $rubros], 200);

        }   catch(\Exception $e){
            return response()->json(['status'=>'success', 'message' => 'No hay oficina'], 500);
    
  
              }      
    }        
}
