<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Propietario as pros;
use App\Models\Propietario as pro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Propietario_files;
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
            
            if ($request->hasFile('fotoVendedor') && count($request->file('fotoVendedor')) > 0)  {
                $archivo=$request->file('fotoVendedor');
                foreach ($request->file('fotoVendedor') as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $uniqueName = date('YmdHis') . rand(10,99);
        
                    $path = $file->storeAs(
    
                        'propietarios/' . date('Y/m'),
                        $uniqueName . '.' . $extension,
                        'public'
                    );
                    $id = $propietario->id;
                    Propietario_files::saveFiles($propietario->id,$filename, $uniqueName, $extension, $path );
                }
            }
            else {
                return response()->json([
                    'message' => 'Se requiere al menos un archivo.',
                ], 422); // Código de error de validación
    
            
        }
    
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
            ->select('p.id AS id', 'p.dni', 'p.nombre', 'p.apellidos', 'pf.path_file')
            ->leftJoin('propietario_files AS pf', 'pf.id_propietario_files', '=', 'p.id')
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