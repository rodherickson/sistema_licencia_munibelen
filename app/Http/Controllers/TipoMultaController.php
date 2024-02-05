<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoMultaModel;

class TipoMultaController extends Controller
{
    public function listTipoMulta(){

        try{

            $tipo_multa=TipoMultaModel::listTipoMulta();
            return response()->json(['status'=>'success', 'data' => $tipo_multa], 200);

        }   catch(\Exception $e){
            return response()->json(['status'=>'success', 'message' => 'No hay oficina'], 500);
    
  
              }      
    }   
}
