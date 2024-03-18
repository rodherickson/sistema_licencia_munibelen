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
            DB::table('carnetexpedidos')->insert([
                'idcarnet' => $carnet->id,
                'fecha' => DB::raw('NOW()')
            ]);

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


    public function obtenerReportePadronVendedores(Request $request)
    {

        // Obtener los datos de la solicitud
        $rubros = $request->input('rubros', []);
        $estados = $request->input('estados', []);
        $distritos = $request->input('distritos', []);
        $fechaEmision = $request->input('fechaEmision', null);

        // Realizar la consulta utilizando Eloquent
        $carnet = DB::table('carnet AS c')
            ->select('c.id AS nro', 'prop.id', 'prop.nombre', 'prop.apellidos', 'prop.dni', 'rub.nombre_rubro', 'prop.direccion', 'c.estado', 'prop.distrito', 'c.fechaEmision', 'c.lugarEstablecimiento')
            ->join('propietario AS prop', 'prop.id', '=', 'c.idpropietario')
            ->join('rubro AS rub', 'rub.id', '=', 'c.idrubro')
            ->whereIn('rub.nombre_rubro', $rubros)
            ->whereIn('c.estado', $estados)
            ->whereIn('prop.distrito', $distritos)
            ->where(function ($query) use ($fechaEmision) {
                if (!is_null($fechaEmision)) {
                    $query->where('c.fechaEmision', '<=', $fechaEmision);
                }
            })
            ->whereIn('c.fechaEmision', function ($query) {
                $query->select(DB::raw('MAX(c2.fechaEmision)'))
                    ->from('carnet AS c2')
                    ->whereColumn('c2.lugarEstablecimiento', 'c.lugarEstablecimiento');
            })
            ->orderBy('c.fechaEmision', 'desc')
            ->get();

        if ($carnet->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron datos con los filtros proporcionados.'
            ]);
        }


        $reportePadronVendedores = [];

        foreach ($carnet as $resultado) {
            $lugarEstablecimiento = $resultado->lugarEstablecimiento;

            // Verificar si ya existe el lugarEstablecimiento en el array
            if (!isset($reportePadronVendedores[$lugarEstablecimiento])) {
                $reportePadronVendedores[$lugarEstablecimiento] = ['lugarEstablecimiento' => $lugarEstablecimiento, 'vendedores' => []];
            }


            $reportePadronVendedores[$lugarEstablecimiento]['vendedores'][] = [
                'nro' => $resultado->nro,
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


    public function contarCarnetsPorMeses()
    {
        // Establecer la configuración regional en español para obtener los nombres de los meses en español
        DB::statement("SET lc_time_names = 'es_ES'");
        $conteoPorMeses = DB::select('
            SELECT 
                YEAR(ce.fecha) AS año,
                MONTHNAME(ce.fecha) AS mes,
                COUNT(*) AS total
            FROM 
                carnet ca
            INNER JOIN 
                carnetexpedidos ce 
            ON 
                ce.idcarnet = ca.id
            WHERE
                ca.estado = "Expedido"
            GROUP BY 
                YEAR(ce.fecha), MONTH(ce.fecha), ce.fecha
            ORDER BY 
                año, MONTH(ce.fecha)
        ');
    
        // Convertir los resultados en un array asociativo para facilitar su uso en la gráfica
        $carnetsExpedidosMensuales = [];
        $carnetsExpedidosAnuales = [];
        foreach ($conteoPorMeses as $row) {
            $año = $row->año;
            $mes = ucfirst(substr($row->mes, 0, 3)); // Obtener las primeras tres letras del nombre del mes
            $total = $row->total;
    
            // Datos mensuales
            $carnetsExpedidosMensuales[$mes] = $total;
    
            // Datos anuales
            if (!isset($carnetsExpedidosAnuales[$año])) {
                $carnetsExpedidosAnuales[$año] = 0;
            }
            $carnetsExpedidosAnuales[$año] += $total;
        }
    
        // Formatear datos mensuales
        $dataMensual = [
            'filtro' => 'Mensual',
            'data' => array_map(function ($mes, $total) {
                return ['label' => $mes, 'value' => $total];
            }, array_keys($carnetsExpedidosMensuales), $carnetsExpedidosMensuales),
        ];
    
        // Formatear datos anuales
        $dataAnual = [
            'filtro' => 'Anual',
            'data' => array_map(function ($año, $total) {
                return ['label' => (string)$año, 'value' => $total];
            }, array_keys($carnetsExpedidosAnuales), $carnetsExpedidosAnuales),
        ];
    
        // Fusionar datos mensuales y anuales
        $data = [$dataMensual, $dataAnual];
    
        return response()->json(['dataCarnetExpedidas'=>$data]);
    }

    public function contarCarnetsPorEstado()
    {
        DB::statement("SET lc_time_names = 'es_ES'");
    
        $carnets = CarnetModel::query()
            ->selectRaw('estado, COUNT(*) AS total')
            ->whereIn('estado', ['Expedido', 'Caducado'])
            ->groupBy('estado')
            ->get()
            ->map(function ($row) {
                $estado = $row->estado;
                $total = $row->total;
    
                return [$estado => ['total' => $total]];
            })
            ->values() // Convertir a un arreglo de valores
            ->toArray();
    
        $data = ['data' => $carnets];
    
        return response()->json(['dataCarnetEstados'=>$data]);
    }
    


    public function actualizarEstadoCarnets()
    {

        $carnets = CarnetModel::all();


        $fechaActual = Carbon::now();

        // Iterar sobre cada carnet y verificar si está caducado
        foreach ($carnets as $carnet) {
            if ($carnet->fecha_caducidad < $fechaActual) {
                $carnet->estado = 'caducado';
                $carnet->save();
            }
        }

        // // Redireccionar o responder según sea necesario
        // return redirect()->route('nombre_de_la_ruta');
    }
}
