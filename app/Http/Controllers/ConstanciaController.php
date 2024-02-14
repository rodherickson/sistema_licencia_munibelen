<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConstanciaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Propietario ;
use Illuminate\Support\Carbon;
class ConstanciaController extends Controller
{
 
    public function expedirConstancia(ConstanciaRequest $request)
{
    try {
        // Validar y procesar la solicitud
        $propietario = Propietario::where('dni', $request->dni)->first();

        if (!$propietario) {
            throw new \Exception('El propietario con el DNI proporcionado no fue encontrado.');
        }

        $fechaEmision = Carbon::createFromFormat('Y/m/d', $request->fechaEmision);
        $fechaCaducidad = $fechaEmision->copy()->addMonths(6);

        // Insertar los datos en la tabla 'constancia' y obtener el ID generado automáticamente
        $constanciaId = DB::table('constancia')->insertGetId([
            'fechaEmision' => $fechaEmision->format('Y/m/d'),
            'fechaCaducidad' => $fechaCaducidad->format('Y/m/d'),
            'idpropietario' => $propietario->id, 
        ]);

        // Construir la respuesta con la información solicitada
        $respuesta = [
            'idconstancia' => $constanciaId,
            'idpropietario' => $propietario->id,
            'fechaEmision' => $fechaEmision->format('Y/m/d'),
            'fechaCaducidad' => $fechaCaducidad->format('Y/m/d'),
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




}
