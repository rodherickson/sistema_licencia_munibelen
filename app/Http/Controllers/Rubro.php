<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rubro as rub;
use Illuminate\Support\Facades\DB;

class Rubro extends Controller
{
    public function listRubro(){

        try{

            $rubrosFromDB = DB::table('rubro')->select('id', 'nombre_rubro')->get();

            // Inicializamos un array vacío para almacenar los rubros formateados
            $rubros = [];
    
            // Iteramos sobre los resultados de la consulta
            foreach ($rubrosFromDB as $rubro) {
                // Formateamos cada rubro y lo agregamos al array de rubros
                $rubros[] = [
                    'value' => $rubro->id,
                    'label' => $rubro->nombre_rubro,
                    
                ];
            }
    
            return response()->json(['rubros' => $rubros]); // Devolvemos los rubros formateados como respuesta JSON
            
        }

           catch(\Exception $e){
            return response()->json(['status'=>'success', 'message' => 'No hay rubro'], 500);
    
  
              }  
              
            
    }        
}
