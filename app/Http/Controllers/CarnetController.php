<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\CarnetRequest;
use App\Models\CarnetModel;
use App\Models\Carnet_files;
use App\Models\Propietario;
use App\Models\Rubro;
use Carbon\Carbon;

class CarnetController extends Controller
{

    public function register(CarnetRequest $request){
        {  
            try {
                DB::beginTransaction();
                
                $propietario = Propietario::create([
                    'nombre'=> $request->nombre,
                    'apellido'=> $request->apellido,
                    'dni'=> $request->dni,
                    'celular'=> $request->celular,
                    'correo'=> $request->correo,
                    'direccion'=> $request->direccion,
                ]);
                
                $fecha_emision = Carbon::createFromFormat('Y/m/d', date('Y/m/d'));
                $fecha_caducidad = $fecha_emision->addMonths(6)->format('Y/m/d');
    

                $Carnet = CarnetModel::create([
                    'idpropietario'=> $propietario->id,
                    'idrubro'=> $request->idrubro,
                    'ubicacion'=> $request->ubicacion,
                    'cuadra'=> $request->cuadra,
                    'largo'=> $request->largo,
                    'ancho'=> $request->ancho,
                    'n_mesa'=> $request->n_mesa,
                    'categoria'=> $request->categoria,
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
    
                            'carnet/' . date('Y/m'),
                            $uniqueName . '.' . $extension,
                            'public'
                        );
                        $id = $Carnet->id;
                        Carnet_files::saveFiles($Carnet->id,$filename, $uniqueName, $extension, $path );
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
