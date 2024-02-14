<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Propietario as pros;
use App\Models\Propietario as pro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class Propietario extends Controller
{

    public function register(pros $request)
    {  
        try {
            DB::beginTransaction();
            
            $propietario = pro::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'dni' => $request->dni,
                'celular' => $request->celular,
                'correo' => $request->correo,
                'direccion' => $request->direccion,
                'distrito' => $request->distrito,
            ]);
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Datos guardados correctamente']);
                    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al guardar los datos: ' . $e->getMessage()], 500);
        }


        
        
    }


    public function mostrarPropietario($dni)
    {
        try {
    $propietario = DB::table('propietario AS p')
    ->select('p.id AS id', 'p.dni', 'p.nombre','p.apellidos',
        DB::raw('CASE WHEN cf.path_file LIKE "%jpg%" OR cf.path_file LIKE "%jpeg%" OR cf.path_file LIKE "%png%" THEN cf.path_file ELSE NULL END AS path_file')
    )
    ->join('carnet AS c', 'p.id', '=', 'c.idpropietario')
    ->leftJoin('carnet_files AS cf', 'c.id', '=', 'cf.id_carnet_files')
    ->where('p.dni', $dni)
    ->first();

if (!$propietario) {
    return response()->json(['success' => false, 'message' => 'No se encontró ningún propietario con el DNI proporcionado.'], 404);
}

return response()->json([
    'success' => true,
    'message' => 'Datos obtenidos correctamente',
    'propietario' => $propietario
]);
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}    