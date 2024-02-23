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
                        Multa_files::saveFiles($detallemulta->id,$filename, $uniqueName, $extension, $path );
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

   
     
}
