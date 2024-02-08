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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CarnetController extends Controller
{

    public function register(CarnetRequest $request){
        {  
            try {
                DB::beginTransaction();
                
                $propietario = Propietario::create([
                    'nombre'=> $request->nombre,
                    'apellidos'=> $request->apellidos,
                    'dni'=> $request->dni,
                    'celular'=> $request->celular,
                    'correo'=> $request->correo,
                    'direccion'=> $request->direccion,
                    'distrito' => $request->distrito,
                ]);
                
                $fechaEmision = Carbon::createFromFormat('Y/m/d', date('Y/m/d'));
                $fechaCaducidad = $fechaEmision->addMonths(6)->format('Y/m/d');
    

                $Carnet = CarnetModel::create([
                    'idpropietario'=> $propietario->id,
                    'idrubro'=> $request->idrubro,
                    'lugarEstablecimiento'=> $request->lugarEstablecimiento,
                    'cuadra'=> $request->cuadra,
                    'largo'=> $request->largo,
                    'ancho'=> $request->ancho,
                    'nroMesa'=> $request->nroMesa,//cambiar a nroMesa
                    'categoria'=> $request->categoria,
                    'fechaEmision'=>  $fechaEmision,//cambiar a fechaEmision
                    'fechaCaducidad'=> $fechaCaducidad,//cambiar a fechaCaducidad
                ]);

                

                if ($request->hasFile('files') && count($request->file('files')) > 0)  {
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

    public function obtenercarnet(Request $request,$dni)
    {   
        if (!is_numeric($dni) || strlen($dni) !== 8) {
            return response()->json(['error' => 'El DNI debe tener exactamente 8 dígitos y ser numérico.'], 400);
        }


        $carnet = DB::table('carnet as c')
                    ->select('p.apellidos', 'p.nombre', 'p.dni', 'p.direccion',
                             'c.lugarEstablecimiento', 'c.cuadra', 'c.largo', 'c.ancho',
                             'r.nombre_rubro as rubro',
                             'c.nroMesa', 'c.categoria', 'c.fechaEmision', 'c.fechaCaducidad',
                             'cf.original_name', 'cf.path_file')
                    ->join('carnet_files as cf', 'c.id', '=', 'cf.id_carnet_files')
                    ->join('propietario as p', 'p.id', '=', 'c.idpropietario')
                    ->join('rubro as r','r.id','=','c.idrubro')
                    ->where('p.dni',$dni)
                    ->where(function ($query) {
                        $query->where('cf.path_file', 'LIKE', '%.jpg')
                              ->orWhere('cf.path_file', 'LIKE', '%.jpeg')
                              ->orWhere('cf.path_file', 'LIKE', '%.png');
                    })
                    ->get();
    
        return response()->json($carnet);
    }

    public function listcarnet()
    {

    $carnet = DB::table('carnet as c')
    ->select('c.id', DB::raw("CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo"), 'r.nombre_rubro as rubro', 'c.fecha_emision', 'c.fecha_caducidad')
    ->join('propietario as p', 'p.id', '=', 'c.idpropietario')
    ->join('rubro as r', 'r.id', '=', 'c.idrubro')
    ->orderBy('c.fecha_caducidad', 'ASC')
    ->get();

return response()->json($carnet);


    }
    
    
    
}
