<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Propietario as pros;
use App\Models\Propietario as pro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class Propietario extends Controller
{
    public function obtenerTodos()
    {
        $propietarios = pro::all();
        return response()->json($propietarios, Response::HTTP_OK);
    }

    public function actualizarDatos($id, pros $request)
    {
        $propietario = pro::find($id);

        if ($propietario) {
            $propietario->update($request->all());
            return response()->json($propietario, Response::HTTP_OK);
        } else {
            return response()->json(['error' => 'Propietario no encontrado'], Response::HTTP_NOT_FOUND);
        }
    }
    
}
