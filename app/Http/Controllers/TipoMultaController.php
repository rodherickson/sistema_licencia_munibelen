<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoMultaModel;
use Illuminate\Support\Facades\DB;

class TipoMultaController extends Controller
{
    public function listTipoMulta(){

        try {
    
            $tipoMultaModel = new TipoMultaModel();
            $tiposMultas = $tipoMultaModel->listTipoMulta();
    
            return response()->json([
                'success' => true,
                'message' => 'Tipos de Multas obtenidos con éxito',
                'tiposMulta' => $tiposMultas,
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
    
        }
    
    }

}
