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
    
}
