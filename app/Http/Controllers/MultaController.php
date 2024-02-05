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


class MultaController extends Controller
{
    public function registerMulta(MultaRequest $request){
        {  
            try {
                DB::beginTransaction();
                
                $licencia = LicenciaModel::where('nombreempresa', $request->nombreempresa)
                ->where('denominacion_local', $request->denominacion_local)
                ->first();

                if (!$licencia) {
                    throw new \Exception('Nombre de la empresa no fue encontrada.');
                }

                $multa = MultaModel::create([
                    'idlicencia' => $licencia->id,
                    'idtipo_multa'=> $request->idtipo_multa,
                    'idarea'=> $request->idarea,
                    'expiredate'=> $request->expiredate,
                ]);
                
                // $fecha_emision = Carbon::createFromFormat('Y/m/d', date('Y/m/d'));
                // $fecha_caducidad = $fecha_emision->addMonths(6)->format('Y/m/d');
    

                $detallemulta = Detalle_MultaModel::create([
                    'idmulta'=> $multa->id,
                    'fecha'=> $request->fecha,
                    'status'=> $request->status,
                    
                ]);

                

                if ($request->hasFile('files') && count($request->file('files')) > 0)  {
                    $archivo=$request->file('files');
                    foreach ($request->file('files') as $file) {
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

   
     
}
