<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\LicenciaRequest;
use App\Models\LicenciaModel;
Use App\Models\Licencia_files;
use Carbon\Carbon;
class LicenciaController extends Controller
{
    public function register(LicenciaRequest $request){
        {  
            try {
                DB::beginTransaction();
                
                
                $fecha_emision = Carbon::createFromFormat('Y/m/d', date('Y/m/d'));
                $fecha_caducidad = $fecha_emision->addMonths(6)->format('Y/m/d');
    

                $Carnet = LicenciaModel::create([
                    'idpropietario'=> $request->idpropietario,
                    'nombreempresa'=> $request->nombreempresa,
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
                        $id = $Carnet->id;
                        Licencia_files::saveFiles($Carnet->id,$filename, $uniqueName, $extension, $path );
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
}
