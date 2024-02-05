<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\LicenciaRequest;
use App\Models\LicenciaModel;
Use App\Models\Licencia_files;
use Carbon\Carbon;
use App\Models\Propietario;
class LicenciaController extends Controller
{
    public function register(LicenciaRequest $request){
        {  
            try {
                DB::beginTransaction();
                
                 // Buscar el propietario basado en el DNI
                $propietario = Propietario::where('dni', $request->dni)->first();

                // Si el propietario no existe, puedes lanzar un error o manejarlo según tus necesidades
                if (!$propietario) {
                    throw new \Exception('El propietario con el DNI proporcionado no fue encontrado.');
                }
                
                $fecha_emision = Carbon::createFromFormat('Y/m/d', date('Y/m/d'));
                $fecha_caducidad = $fecha_emision->addMonths(6)->format('Y/m/d');
                

                $licencia = LicenciaModel::create([
                    'idpropietario' => $propietario->id,
                    'nombreempresa'=> $request->nombreempresa,
                    'denominacion_local'=> $request->denominacion_local,
                    'ruc'=> $request->ruc,
                    'direccion'=> $request->direccion,
                    'area'=> $request->area,
                    'aforo'=> $request->aforo,
                    'fecha_emision'=>  $fecha_emision,
                    'fecha_caducidad'=> $fecha_caducidad,
                ]);
                
                if ($request->hasFile('files')) {
                    $archivo=$request->file('files');
                    foreach ($request->file('files') as $file) {
                        $filename = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $uniqueName = date('YmdHis') . rand(10,99);
            
                        $path = $file->storeAs(
    
                            'licencia/' . date('Y/m'),
                            $uniqueName . '.' . $extension,
                            'public'
                        );
                        $id = $licencia->id;
                        Licencia_files::saveFiles($licencia->id,$filename, $uniqueName, $extension, $path );
                    }
                }
    
                DB::commit();
    
                return response()->json([
                    'message' => 'Datos guardados',
                ], 200);
                
            } catch (\Throwable $e){
                DB::rollBack();
                return response()->json([
                    'status' =>'error',
                    'message' =>$e->getMessage()
                ], 500);
            }
        }
    }

    public function obtnerlicencia(Request $request,$dni){
     
        if (!is_numeric($dni) || strlen($dni) !== 8) {
            return response()->json(['error' => 'El DNI debe tener exactamente 8 dígitos y ser numérico.'], 400);
        }


        {
            $licencia = DB::table('licencia as l')
                        ->join('propietario as p', 'p.id', '=', 'l.idpropietario')
                        ->select('p.dni', 'p.nombre', 'p.apellido', 'l.nombreempresa', 'l.denominacion_local', 'l.ruc', 'l.direccion', 'l.area', 'l.aforo', 'l.fecha_emision')
                        ->where('p.dni', $dni)
                        ->first();
    
            if ($licencia) {
                return response()->json(['success' => true, 'licencia' => $licencia], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontró ninguna licencia para el DNI proporcionado.'], 404);
            }
        }
    }
}
