<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConstanciaRequest;
use App\Models\Constancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Propietario;
use Illuminate\Support\Carbon;

class ConstanciaController extends Controller
{

    public function expedirConstancia(ConstanciaRequest $request)
    {
        try {
            $propietario = Propietario::where('dni', $request->dni)->first();

            if (!$propietario) {
                throw new \Exception('El propietario con el DNI proporcionado no fue encontrado.');
            }

            // Verificar si ya existe una constancia expedida para este propietario
            $constanciaExpedida = DB::table('constancia')
                ->where('idpropietario', $propietario->id)
                ->where('estado', 'Expedido')
                ->exists();

            if ($constanciaExpedida) {
                return response()->json(['success' => false, 'message' => 'Ya se ha expedido una constancia para este propietario.'], 400);
            }

            $fechaEmision = Carbon::createFromFormat('Y-m-d', $request->fechaEmision);
            $fechaCaducidad = $fechaEmision->copy()->addMonths(6);

            // Insertar los datos en la tabla 'constancia' y obtener el ID generado automáticamente
            $constanciaId = DB::table('constancia')->insertGetId([
                'fechaEmision' => $fechaEmision->format('Y-m-d'),
                'fechaCaducidad' => $fechaCaducidad->format('Y-m-d'),
                'idpropietario' => $propietario->id,
            ]);

            DB::table('constanciaexpedidos')->insert([
                'idconstancia' => $constanciaId,
                'fecha' => DB::raw('NOW()')
            ]);

            DB::table('constancia')->where('id', $constanciaId)->update(['estado' => 'Expedido']);

            $respuesta = [
                'idconstancia' => $constanciaId,
                'idpropietario' => $propietario->id,
                'fechaEmision' => $fechaEmision->format('Y-m-d'),
                'fechaCaducidad' => $fechaCaducidad->format('Y-m-d'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Datos obtenidos correctamente',
                'constancia' => $respuesta
            ]);
        } catch (\Exception $e) {
            // Manejo de la excepción
            return response()->json(['success' => false, 'message' => 'Se produjo un error al obtener la constancia: ' . $e->getMessage()], 500);
        }
    }


    public function listconstanciaCaducados(Request $request)
    {
        try {
            $request->validate([
                'numberItems' => 'required|numeric',
                'page' => 'required|numeric'
            ]);

            $constancia = DB::table('constancia as c')
                ->select('c.id', 'p.nombre', 'p.apellidos', 'p.dni', 'c.fechaEmision', 'c.fechaCaducidad', 'c.estado')
                ->join('propietario as p', 'p.id', '=', 'c.idpropietario')
                ->where('c.estado', 'Caducado')
                ->orderBy('c.fechaCaducidad', 'ASC')
                ->paginate($request->numberItems, ['*'], 'page', $request->page);

            return response()->json([
                'success' => true,
                'message' => 'constancias caducados obtenidos con éxito',
                'constancia' => $constancia->items(),
                'currentPage' => $constancia->currentPage(),
                'totalPages' => $constancia->lastPage(),
                'perPage' => $constancia->perPage(),
                'total' => $constancia->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los constancias: ' . $e->getMessage()
            ], 500);
        }
    }



    public function updateConstancia(Request $request, $idConstancia)
    {
        try {
            DB::beginTransaction();

            // Encuentra la constancia que se va a actualizar
            $constancia = Constancia::findOrFail($idConstancia);

            // Actualiza la fecha de emisión si se proporciona en la solicitud
            if ($request->has('fechaEmision')) {
                $fechaEmision = Carbon::createFromFormat('Y-m-d', $request->fechaEmision);
                $constancia->fechaEmision = $fechaEmision->format('Y-m-d');
            }

            // Calcula la fecha de caducidad basada en la fecha de emisión
            if ($request->has('fechaEmision')) {
                $fechaEmision = Carbon::createFromFormat('Y-m-d', $request->fechaEmision);
                $fechaCaducidad = $fechaEmision->copy()->addMonths(6);
                $constancia->fechaCaducidad = $fechaCaducidad->format('Y-m-d');
            }

            // Actualiza el estado si se proporciona en la solicitud
            if ($request->has('estado')) {
                $constancia->estado = $request->estado;
            }
            // Guarda la constancia actualizada
            $constancia->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Constancia actualizada correctamente.',
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
