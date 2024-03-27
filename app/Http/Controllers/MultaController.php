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
    public function registerMulta(MultaRequest $request)
    { {
            try {
                DB::beginTransaction();

                $licencia = LicenciaModel::where('idnombrecomercial', $request->idNombreComercial)
                    ->first();

                if (!$licencia) {
                    throw new \Exception('el nombre comercial no fue encontrada.');
                }

                $fechaEmisionMulta = Carbon::createFromFormat('Y-m-d', $request->fechaEmisionMulta);
                $expiredate = $fechaEmisionMulta->copy()->addWeekdays(6);

                $multa = MultaModel::create([
                    'idlicencia' => $licencia->id,
                    'idtipoMulta' => $request->idTipoMulta,
                    'expiredate' => $expiredate->format('Y-m-d'),
                ]);


                // $fecha_emision = Carbon::createFromFormat('Y/m/d', date('Y/m/d'));
                // $fecha_caducidad = $fecha_emision->addMonths(6)->format('Y/m/d');


                $detallemulta = Detalle_MultaModel::create([
                    'idmulta' => $multa->id,
                    'fecha' => $request->fechaEmisionMulta,
                    'status' => $request->condicion,

                ]);

                if ($request->hasFile('anexosAdjuntos') && count($request->file('anexosAdjuntos')) > 0) {
                    $archivo = $request->file('anexosAdjuntos');
                    foreach ($request->file('anexosAdjuntos') as $file) {
                        $filename = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $uniqueName = date('YmdHis') . rand(10, 99);

                        $path = $file->storeAs(

                            'multa/' . date('Y/m'),
                            $uniqueName . '.' . $extension,
                            'public'
                        );
                        $id = $detallemulta->id;
                        Multa_files::saveFiles($detallemulta->id, $filename, $uniqueName, $extension, $path, auth()->user()->id);
                    }
                } else {
                    return response()->json([
                        'message' => 'Se requiere al menos un archivo.',
                    ], 422); // Código de error de validación


                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Datos guardados correctamente',
                ], 200);
            } catch (\Throwable $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        }
    }

    function compararMeses($a, $b)
    {
        $mesesOrdenados = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        return array_search($a, $mesesOrdenados) - array_search($b, $mesesOrdenados);
    }


    public function obtenerDatosTipoMultas()
    {
        // Consulta para obtener el recuento de multas por tipo, año y mes
        $multas = DB::table('multa as m')
            ->join('detalle_multa as dm', 'dm.idmulta', '=', 'm.id')
            ->join('tipo_multa as tm', 'tm.id', '=', 'm.idtipoMulta')
            ->select(
                DB::raw('YEAR(dm.fecha) AS año'),
                DB::raw('MONTHNAME(dm.fecha) AS mes'),
                'tm.nombreMulta',
                DB::raw('COUNT(*) as totalMultas')
            )
            ->groupBy('año', 'mes', 'tm.nombreMulta')
            ->orderBy('año', 'asc')
            ->orderByRaw('MONTH(dm.fecha)')
            ->orderBy('tm.nombreMulta', 'asc')
            ->get();

        // Inicializar arrays para almacenar los datos
        $meses = [];
        $dataMensual = [
            'filtro' => 'Mensual',
            'label' => [],
            'data' => [],
        ];

        $dataAnual = [
            'filtro' => 'Anual',
            'label' => [],
            'data' => [],
        ];

        // Procesar los resultados de la consulta y construir las etiquetas de los meses
        foreach ($multas as $multa) {
            if (!in_array($multa->mes, $meses)) {
                $meses[] = $multa->mes;
            }
        }

        // Ordenar los meses por su posición en el año
        usort($meses, function ($a, $b) {
            $mesesOrdenados = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            return array_search($a, $mesesOrdenados) - array_search($b, $mesesOrdenados);
        });

        // Llenar las etiquetas de meses para el filtro mensual
        $dataMensual['label'] = array_map(function ($mes) {
            return substr($mes, 0, 3);
        }, $meses);

        // Procesar los resultados de la consulta
        foreach ($multas as $multa) {
            // Verificar si ya existe una entrada para este tipo de multa en la parte mensual
            $tipoMultaIndex = array_search($multa->nombreMulta, array_column($dataMensual['data'], 'label'));
            if ($tipoMultaIndex === false) {
                // Si no existe, agregar una nueva entrada
                $dataMensual['data'][] = [
                    'label' => $multa->nombreMulta,
                    'value' => array_fill(0, count($meses), 0), // Inicializar con 0 multas para cada mes
                ];
                $tipoMultaIndex = count($dataMensual['data']) - 1;
            }

            // Actualizar el valor de la multa para el mes correspondiente
            $mesIndex = array_search(substr($multa->mes, 0, 3), $dataMensual['label']);
            if ($mesIndex !== false) {
                $dataMensual['data'][$tipoMultaIndex]['value'][$mesIndex] += $multa->totalMultas;
            }
        }

        // Obtener los años únicos
        $añosUnicos = array_unique(array_column($multas->toArray(), 'año'));
        sort($añosUnicos);

        // Llenar las etiquetas de años para el filtro anual
        $dataAnual['label'] = $añosUnicos;

        // Llenar los datos anuales con valores de multas en cero para cada año y tipo de multa
        foreach ($dataMensual['data'] as $tipoMultaData) {
            $dataAnual['data'][] = [
                'label' => $tipoMultaData['label'],
                'value' => array_fill(0, count($añosUnicos), 0), // Inicializar con 0 multas para cada año
            ];

            // Recorrer todos los años y sumar el total de multas para este tipo de multa en cada año
            foreach ($multas as $multa) {
                if ($multa->nombreMulta === $tipoMultaData['label']) {
                    $añoIndex = array_search($multa->año, $añosUnicos);
                    if ($añoIndex !== false) {
                        $dataAnual['data'][count($dataAnual['data']) - 1]['value'][$añoIndex] += $multa->totalMultas;
                    }
                }
            }
        }

        // Combinar datos mensuales y anuales
        $dataTipoMultas = [$dataMensual, $dataAnual];

        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'dataTipoMultas' => $dataTipoMultas
        ]);
    }



    public function updateMulta(Request $request, $idMulta)
    {
        try {
            DB::beginTransaction();

            // Encuentra la multa que se va a actualizar
            $multa = MultaModel::findOrFail($idMulta);

            // Encuentra o crea el detalle de la multa
            $detalleMulta = Detalle_MultaModel::where('idmulta', $multa->id)->first();

            if (!$detalleMulta) {
                // Manejar el caso cuando el detalle de la multa no existe
                return response()->json([
                    'success' => false,
                    'message' => 'Detalle de multa no encontrado para la multa dada.',
                ], 404);
            }

            // Verifica si se adjuntaron más archivos
            if ($request->hasFile('anexosAdjuntos') && count($request->file('anexosAdjuntos')) > 0) {
                foreach ($request->file('anexosAdjuntos') as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $uniqueName = date('YmdHis') . rand(10, 99);

                    $path = $file->storeAs(
                        'multa/' . date('Y/m'),
                        $uniqueName . '.' . $extension,
                        'public'
                    );

                    // Guarda los detalles del archivo adjunto junto con el ID del detalle de la multa
                    Multa_files::saveFiles($detalleMulta->id, $filename, $uniqueName, $extension, $path, $request->user()->id);
                }
            }

           
            // Actualiza otros campos de la multa si es necesario
            $detalleMulta->update([
                'status' => $request->condicion
            ]);


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Actualizado correctamente la multa',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function datosMulta($idmulta)
    {
        // Ejecutar la consulta SQL
        $datosMulta = DB::select("
            SELECT mu.id, dm.status as estado, GROUP_CONCAT(fm.path_file) AS path_files
            FROM multa mu 
            INNER JOIN detalle_multa dm ON dm.idmulta = mu.id 
            INNER JOIN files_multas fm ON fm.id_multade_files = dm.id 
            WHERE mu.id = :id
            GROUP BY mu.id, dm.status;
        ", ['id' => $idmulta]);

        return response()->json([
            'success' => true,
            'message' => 'Datos obtenidos correctamente',
            'datosMulta' => $datosMulta
        ], 200);
    }




    public function listarMultasEnProceso(Request $request){
        try {
            $request->validate([
                'numberItems' => 'required|numeric',
                'page' => 'required|numeric'
            ]);

            $multas = DB::table('multa as mu')
            ->select('mu.id','p.dni','nc.nombreComercial', 'tm.nombreMulta', 'dm.fecha', 'dm.status')
            ->join('detalle_multa as dm', 'mu.id', '=', 'dm.idmulta')
            ->join('tipo_multa as tm', 'mu.idtipoMulta', '=', 'tm.id')
            ->join('licencia as li', 'mu.idlicencia', '=', 'li.id')
            ->join('nombrescomerciales as nc', 'li.idnombreComercial', '=', 'nc.id')
            ->join('propietario as p', 'li.idpropietario', '=', 'p.id')
            ->where('dm.status', 'En Proceso')
            ->orderBy('dm.fecha', 'ASC')
                ->paginate($request->numberItems, ['*'], 'page', $request->page);

            return response()->json([
                'success' => true,
                'message' => 'Multas obtenidos con éxito',
                'multas' => $multas->items(),
                'currentPage' => $multas->currentPage(),
                'totalPages' => $multas->lastPage(),
                'perPage' => $multas->perPage(),
                'total' => $multas->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las Multas: ' . $e->getMessage()
            ], 500);
        }
    }

    }

