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

   

    public function register(CarnetRequest $request)
    {
        try {

            $propietario = Propietario::where('dni', $request->dni)->first();

            $fechaEmision = Carbon::createFromFormat('Y-m-d', $request->fechaEmision);
            $fechaCaducidad = $fechaEmision->copy()->addMonths(6);

            $carnet = CarnetModel::create([
                'idpropietario' => $propietario->id,
                'idrubro' => $request->idrubro,
                'lugarEstablecimiento' => $request->lugarEstablecimiento,
                'cuadra' => $request->cuadra,
                'largo' => $request->largo,
                'ancho' => $request->ancho,
                'nroMesa' => $request->nroMesa, //cambiar a nroMesa
                'categoria' => $request->categoria,
                'fechaEmision' => $fechaEmision->format('Y-m-d'),
                'fechaCaducidad' => $fechaCaducidad->format('Y-m-d'),
            ]);

            if ($request->hasFile('anexosAdjuntos') && count($request->file('anexosAdjuntos')) > 0) {
                $archivo = $request->file('anexosAdjuntos');
                foreach ($request->file('anexosAdjuntos') as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $uniqueName = date('YmdHis') . rand(10, 99);

                    $path = $file->storeAs(

                        'carnet/' . date('Y/m'),
                        $uniqueName . '.' . $extension,
                        'public'
                    );
                    $id = $propietario->id;
                    Carnet_files::saveFiles($carnet->id, $filename, $uniqueName, $extension, $path);
                }
            } else {
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

    public function obtenercarnet(Request $request, $dni)
{
    try {
        if (!is_numeric($dni) || strlen($dni) !== 8) {
            return response()->json(['error' => 'El DNI debe tener exactamente 8 dígitos y ser numérico.'], 400);
        }

        $carnet = DB::table('carnet as c')
        ->select(
            'c.id',
            'p.apellidos',
            'p.nombre',
            'p.dni',
            'p.direccion',
            'c.lugarEstablecimiento',
            'c.cuadra',
            'c.largo',
            'c.ancho',
            'r.nombre_rubro as rubro',
            'c.nroMesa',
            'c.categoria',
            'c.fechaEmision',
            'c.fechaCaducidad',
            'c.estado',
            'pf.original_name',
            'pf.path_file'
        )
        ->join('propietario as p', 'p.id', '=', 'c.idpropietario')
        ->join('propietario_files as pf', 'pf.id_propietario_files', '=', 'p.id')
        ->join('rubro as r', 'r.id', '=', 'c.idrubro')
        ->where('p.dni', $dni)
        ->where(function ($query) {
            $query->where('pf.path_file', 'LIKE', '%.jpg')
                ->orWhere('pf.path_file', 'LIKE', '%.jpeg')
                ->orWhere('pf.path_file', 'LIKE', '%.png');
        })
        ->get();

        if ($carnet->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No se encontró ningún carnet con el DNI especificado.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'carnet' => $carnet
        ]);
    } catch (\Exception $e) {
        // Manejo de la excepción
        return response()->json(['success' => false, 'message' => 'Se produjo un error al obtener el carnet: ' . $e->getMessage()], 500);
    }
}


    public function listcarnet(Request $request)
    {
        try {
            $request->validate([
                'numberItems' => 'required|numeric',
                'page' => 'required|numeric'
            ]);

            $carnet = DB::table('carnet as c')
                ->select('c.id', 'p.nombre', 'p.apellidos', 'p.dni', 'r.nombre_rubro as rubro', 'c.fechaEmision', 'c.fechaCaducidad')
                ->join('propietario as p', 'p.id', '=', 'c.idpropietario')
                ->join('rubro as r', 'r.id', '=', 'c.idrubro')
                ->orderBy('c.fechaCaducidad', 'ASC')
                ->paginate($request->numberItems, ['*'], 'page', $request->page);

            return response()->json([
                'success' => true,
                'message' => 'Carnet obtenidos con éxito',
                'carnet' => $carnet->items(),
                'currentPage' => $carnet->currentPage(),
                'totalPages' => $carnet->lastPage(),
                'perPage' => $carnet->perPage(),
                'total' => $carnet->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los carnets: ' . $e->getMessage()
            ], 500);
        }
    }


    public function expedirCarnet(Request $request, $dni)
    {
        try {
            if (!is_numeric($dni) || strlen($dni) !== 8) {
                return response()->json(['error' => 'El DNI debe tener exactamente 8 dígitos y ser numérico.'], 400);
            }

            $carnet = DB::table('carnet as c')
                ->select('c.id', 'p.dni')
                // ->join('carnet_files as cf', 'c.id', '=', 'cf.id_carnet_files')
                ->join('propietario as p', 'p.id', '=', 'c.idpropietario')
                // ->join('rubro as r','r.id','=','c.idrubro')
                ->where('p.dni', $dni)
                ->first(); // Obtener el primer carnet que coincida con el DNI

            if (!$carnet) {
                return response()->json(['success' => false, 'message' => 'No se encontró ningún carnet con el DNI especificado.'], 404);
            }

            // Insertar el idcarnet en la tabla carnetexpedidos
            DB::table('carnetexpedidos')->insert(['idcarnet' => $carnet->id]);

            // Actualizar el estado del carnet a "Expedido"
            DB::table('carnet')->where('id', $carnet->id)->update(['estado' => 'Expedido']);

            return response()->json([
                'success' => true,
                'message' => 'Carnet expedido'
            ]);
        } catch (\Exception $e) {
            // Manejo de la excepción
            return response()->json(['success' => false, 'message' => 'Se produjo un error al obtener el carnet. '], 500);
        }
    }


    public function obtenerReportePadronVendedores()
    {
        // Realizar la consulta utilizando Eloquent o Query Builder
        $carnet = DB::table('carnet AS c')
            ->select('prop.id', 'prop.nombre', 'prop.apellidos', 'prop.dni', 'rub.nombre_rubro', 'prop.direccion', 'c.estado', 'prop.distrito', 'c.fechaEmision', 'c.lugarEstablecimiento')
            ->join('propietario AS prop', 'prop.id', '=', 'c.idpropietario')
            ->join('rubro AS rub', 'rub.id', '=', 'c.idrubro')
            ->get();
    
        // Organizar los resultados en la estructura deseada
        $reportePadronVendedores = [];
    
        foreach ($carnet as $resultado) {
            $lugarEstablecimiento = $resultado->lugarEstablecimiento;
    
            // Verificar si ya existe el lugarEstablecimiento en el array
            if (!isset($reportePadronVendedores[$lugarEstablecimiento])) {
                $reportePadronVendedores[$lugarEstablecimiento] = ['lugarEstablecimiento' => $lugarEstablecimiento, 'vendedores' => []];
            }
    
            // Agregar los datos del vendedor al lugarEstablecimiento correspondiente
            $reportePadronVendedores[$lugarEstablecimiento]['vendedores'][] = [
                'nro' => count($reportePadronVendedores[$lugarEstablecimiento]['vendedores']) + 1,
                'nombre' => $resultado->nombre,
                'apellidos' => $resultado->apellidos,
                'dni' => $resultado->dni,
                'rubro' => $resultado->nombre_rubro,
                'direccion' => $resultado->direccion,
                'condicion' => $resultado->estado,
                'distrito' => $resultado->distrito,
            ];
        }
    
        // Devolver los datos en formato JSON
        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'reportePadronVendedores' => array_values($reportePadronVendedores)
        ]);
    }

}
