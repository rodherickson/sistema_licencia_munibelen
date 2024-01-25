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
                $rubro = Rubro::firstOrNew(
                    ['nombre_rubro' => $request->nombre_rubro],
                    ['descripcion' => $request->descripcion, 'estado' => $request->estado]
                );
            
                // Guardar el rubro si es nuevo
                if (!$rubro->exists) {
                    $rubro->save();
                }
                $fecha_emision = Carbon::createFromFormat('Y/m/d', $request->fecha_emision);
                $fecha_caducidad = $fecha_emision->addMonths(6)->format('Y/m/d');
    

                $Carnet = CarnetModel::create([
                    'idpropietario'=> $propietario->id,
                    'idrubro'=> $rubro->id,
                    'ubicacion'=> $request->ubicacion,
                    'cuadra'=> $request->cuadra,
                    'largo'=> $request->largo,
                    'ancho'=> $request->ancho,
                    'n_mesa'=> $request->n_mesa,
                    'categoria'=> $request->categoria,
                    'fecha_emision'=> $request->fecha_emision,
                    'fecha_caducidad'=> $fecha_caducidad,
                ]);
    
                DB::commit();
    
                return response()->json([
                    'message' => 'Datos guardados',
                ], 200);
                
            } catch (\Throwable $e){
                DB::rollBack();
                return response()->json([
                    'message' =>$e->getMessage()
                ], 500);
            }
        }
    
    }
}
