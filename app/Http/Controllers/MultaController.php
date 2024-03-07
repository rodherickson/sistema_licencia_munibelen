<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Propietario;
use App\Models\Multa_files;
use App\Models\MultaModel;
use App\Models\Detalle_MultaModel;
use App\Http\Requests\MultaRequest;
use App\Models\LicenciaModel;
use Illuminate\Support\Carbon;

class MultaController extends Controller
{
    public function registerMulta(MultaRequest $request){
        {  
            try {
                DB::beginTransaction();
                
                $licencia = LicenciaModel::where('idnombrecomercial', $request->idnombrecomercial)
                    ->first();
                
                if (!$licencia) {
                    throw new \Exception('el nombre comercial no fue encontrada.');
                }
                
                $fecha = Carbon::createFromFormat('Y-m-d', $request->fecha);
                $expiredate = $fecha->copy()->addWeekdays(6);                

                $multa = MultaModel::create([
                    'idlicencia' => $licencia->id,
                    'idtipoMulta' => $request->idtipoMulta,
                    'expiredate' => $expiredate->format('Y-m-d'),
                ]);
                
                
                // $fecha_emision = Carbon::createFromFormat('Y/m/d', date('Y/m/d'));
                // $fecha_caducidad = $fecha_emision->addMonths(6)->format('Y/m/d');
    

                $detallemulta = Detalle_MultaModel::create([
                    'idmulta'=> $multa->id,
                    'fecha'=> $request->fecha,
                    'status'=> $request->status,
                    
                ]);

                

                if ($request->hasFile('anexosAdjuntos') && count($request->file('anexosAdjuntos')) > 0)  {
                    $archivo=$request->file('anexosAdjuntos');
                    foreach ($request->file('anexosAdjuntos') as $file) {
                        $filename = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $uniqueName = date('YmdHis') . rand(10,99);
            
                        $path = $file->storeAs(
    
                            'multa/' . date('Y/m'),
                            $uniqueName . '.' . $extension,
                            'public'
                        );
                        $id = $detallemulta->id;
                        Multa_files::saveFiles($detallemulta->id,$filename, $uniqueName, $extension, $path,auth()->user()->id );
                    }
                }
                else {
                    return response()->json([
                        'message' => 'Se requiere al menos un archivo.',
                    ], 422); // Código de error de validación
    
                
            }

            DB::commit();
    
                return response()->json([
                    'success'=>true,
                    'message' => 'Datos guardados correctamente',
                ], 200);
                
            } catch (\Throwable $e){
                DB::rollBack();
                return response()->json([
                    'success' =>false,
                    'message' =>$e->getMessage()
                ], 500);
            }
        }
    
    }


    public function updateMulta(Request $request, $idMulta) {
        try {
            DB::beginTransaction();
            
            // Encuentra la multa que se va a actualizar
            $multa = MultaModel::findOrFail($idMulta);
            
            // Guarda los archivos adjuntos si se proporcionan en la solicitud
            if ($request->hasFile('anexosAdjuntos') && count($request->file('anexosAdjuntos')) > 0)  {
                foreach ($request->file('anexosAdjuntos') as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $uniqueName = date('YmdHis') . rand(10,99);
    
                    $path = $file->storeAs(
                        'multa/' . date('Y/m'),
                        $uniqueName . '.' . $extension,
                        'public'
                    );
                    Multa_files::saveFiles($multa->id, $filename, $uniqueName, $extension, $path,auth()->user()->id);
                }
            }
            
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Archivos adjuntos de la multa actualizados correctamente',
            ], 200);
        } catch (\Throwable $e){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function consultarMultasPorTipoYFecha()
    {
        // Ejecutar la consulta para establecer la configuración local a español
        DB::statement("SET lc_time_names = 'es_ES'");
    
        // Consulta para obtener el conteo de multas por mes y tipo
        $multasPorMes = DB::table('multa as m')
            ->join('detalle_multa as dm', 'dm.idmulta', '=', 'm.id')
            ->join('tipo_multa as tm', 'tm.id', '=', 'm.idtipoMulta')
            ->select(DB::raw('MONTHNAME(dm.fecha) as month'), 'tm.nombreMulta', DB::raw('COUNT(*) as totalMultas'))
            ->groupBy('month', 'tm.nombreMulta')
            ->orderBy('month')
            ->get();
    
        // Formatear los resultados según el formato deseado
        $resultadoFormateado = [];
    
        foreach ($multasPorMes as $multa) {
            // Obtener el nombre del mes en español
            $nombreMes = $multa->month;
    
            // Crear la entrada en el array asociativo si no existe
            if (!isset($resultadoFormateado[$nombreMes])) {
                $resultadoFormateado[$nombreMes] = [];
            }
    
            // Agregar los detalles de la multa al resultado formateado
            $resultadoFormateado[$nombreMes][] = [
                'nombreMulta' => $multa->nombreMulta,
                'totalMultas' => $multa->totalMultas,
            ];
        }
    
        return $resultadoFormateado;
    }
    
    }
   
     

