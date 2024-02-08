<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AreaModel;

class AreaController extends Controller
{
    public function listArea(){

        try{

            $areas=AreaModel::listArea();
            return response()->json(['status'=>'success', 'data' => $areas], 200);

        }   catch(\Exception $e){
            return response()->json(['status'=>'success', 'message' => 'No hay oficina'], 500);
    
  
              }      
    }       
}
