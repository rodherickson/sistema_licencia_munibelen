<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\LicenciaRequest;
use App\Models\LicenciaModel;
use App\Models\Licencia_files;
use Carbon\Carbon;
use App\Models\Propietario;
use App\Models\NombrecomercialModel;
use App\Models\RazonesocialesModel;

class LicenciaController extends Controller
{
    public function register(LicenciaRequest $request)
{
    try {
        DB::beginTransaction();

        // Buscar el propietario basado en el DNI
        $propietario = Propietario::where('dni', $request->dni)->first();

        // Si el propietario no existe, puedes lanzar un error o manejarlo según tus necesidades
        if (!$propietario) {
            throw new \Exception('El propietario con el DNI proporcionado no fue encontrado.');
        }

        // Verificar si la razón social ya existe en la tabla
        $razonSocial = RazonesocialesModel::where('razonSocial', $request->razonSocial)->first();

        // Si la razón social no existe, la creamos
        if (!$razonSocial) {
            $razonSocial = RazonesocialesModel::create([
                'razonSocial' => $request->razonSocial,
            ]);
        }

        // Verificar si el nombre comercial ya existe en la tabla
        $nombreComercial = NombrecomercialModel::where('nombreComercial', $request->nombreComercial)->first();

        // Si el nombre comercial no existe, lo creamos
        if (!$nombreComercial) {
            $nombreComercial = NombrecomercialModel::create([
                'nombreComercial' => $request->nombreComercial,
            ]);
        }

        $fechaEmision = Carbon::now();
        $fechaCaducidad = Carbon::now()->addMonths(6);

        $licencia = LicenciaModel::create([
            'idpropietario' => $propietario->id,
            'idrubro' => $request->idrubro,
            'idrazonSocial' => $razonSocial->id,
            'idnombreComercial' => $nombreComercial->id,
            'ruc' => $request->ruc,
            'direccionEstablecimiento' => $request->direccionEstablecimiento,
            'distritoEstablecimiento' => $request->distritoEstablecimiento,
            'area' => $request->area,
            'inspector' => $request->inspector,
            'aforo' => $request->aforo,
            'fechaEmision' => $fechaEmision,
            'fechaCaducidad' => $fechaCaducidad,
        ]);

       
        if ($request->hasFile('anexosAdjuntos') && count($request->file('anexosAdjuntos')) > 0)  {
            $archivo=$request->file('anexosAdjuntos');
            foreach ($request->file('anexosAdjuntos') as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $uniqueName = date('YmdHis') . rand(10,99);
    
                $path = $file->storeAs(

                    'licencia/' . date('Y/m'),
                    $uniqueName . '.' . $extension,
                    'public'
                );
                $id = $licencia->id;
                Licencia_files::saveFiles($licencia->id,$filename, $uniqueName, $extension, $path );
            }
        }
        else {
            return response()->json([
                'message' => 'Se requiere al menos un archivo.',
            ], 422); // Código de error de validación

        
    }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Datos guardados',
        ], 200);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'status' => 'error al guardar los datos',
            'message' => $e->getMessage()
        ], 500);
    }
}

    //areglar crear tabla 
    public function obtnerlicencia(Request $request, $dni)
    {
        try{

        if (!is_numeric($dni) || strlen($dni) !== 8) {
            return response()->json(['error' => 'El DNI debe tener exactamente 8 dígitos y ser numérico.'], 400);
        }
        $consulta = DB::table('propietario as p')
            ->join('carnet as c', 'p.id', '=', 'c.idpropietario')
            ->join('carnet_files as cf', 'c.id', '=', 'cf.id_carnet_files')
            ->join('licencia as li', 'p.id', '=', 'li.idpropietario')
            ->join('rubro as ru', 'ru.id', '=', 'li.idrubro')
            ->join('razonesociales as ra', 'ra.id', '=', 'li.idrazonsocial')
            ->join('nombrescomerciales as no', 'no.id', '=', 'li.idnombreComercial')
            ->select(
                'p.id as id_propietario',
                'p.dni',
                'p.nombre',
                'p.apellidos',
                'cf.path_file as foto',
                'p.direccion',
                'li.id as id_licencia',
                'ra.razonSocial',
                'idrazonsocial as id_razonSocial',
                'no.nombreComercial',
                'li.idnombreComercial as id_nombreComercial',
                'li.ruc',
                'li.direccionEstablecimiento as direccion del Establecimiento',
                'li.area',
                'ru.id as id_rubro',
                'ru.nombre_rubro as rubro',
                'li.aforo',
                'li.fechaEmision',
                'li.fechaCaducidad'
            )
            ->where('p.dni', $dni)
            ->whereRaw("(cf.path_file LIKE '%.jpg' OR cf.path_file LIKE '%.jpeg' OR cf.path_file LIKE '%.png')")
            ->get();

        if ($consulta->isEmpty()) {
            return response()->json(['error' => 'No se encontraron datos para el DNI proporcionado.'], 404);
        }

        $propietario = [
            'dni' => $consulta[0]->dni,
            'nombre' => $consulta[0]->nombre,
            'apellidos' => $consulta[0]->apellidos,
            'foto' => $consulta[0]->foto,
            'direccion' => $consulta[0]->direccion,
            'establecimientos' => []
        ];

        $empresas = [];
        foreach ($consulta as $item) {
            $id_razonSocial = $item->id_razonSocial;
            $razonSocial = $item->razonSocial;
            $denominacionLocal = $item->nombreComercial;
            $id_nombreComercial = $item->id_nombreComercial;
            $rubro = $item->rubro;
            $id_rubro = $item->id_rubro;

            if (!isset($empresas[$razonSocial])) {
                $empresas[$razonSocial] = [
                    'id' => $id_razonSocial,
                    'razonSocial' => $razonSocial,
                    'marcas' => []
                ];
            }

            // Construir la estructura de cada marca
            $marca = [
                'id' => $id_nombreComercial,
                'nombreComercial' => $denominacionLocal,
                'rubro' => [
                    'id' => $id_rubro,
                    'nombreRubro' => $rubro
                ]
            ];

            // Agregar la marca si no existe en el array de marcas de la empresa
            $marcaExiste = false;
            foreach ($empresas[$razonSocial]['marcas'] as $existingMarca) {
                if ($existingMarca['id'] === $id_nombreComercial) {
                    $marcaExiste = true;
                    break;
                }
            }

            if (!$marcaExiste) {
                $empresas[$razonSocial]['marcas'][] = $marca;
            }
        }

        $propietario['establecimientos'] = array_values($empresas);

        return response()->json([
        'success' => true,
        'message' => 'Datos obtenidos correctamente',
        'propietario' => $propietario]);
    }

     catch (\Exception $e)
    {
        return response()->json(['success' => false, 'message' => 'Se produjo un error al obtener la licencia. '], 500);
    }

    }

    public function expedirLicencia($id){

        try {
            $licencia = DB::table('licencia as li')
                ->select('li.id', 'ra.razonSocial', 'ru.nombre_rubro', 'nom.nombreComercial as denominado', 'li.area', 'li.direccionEstablecimiento', 'li.fechaCaducidad as Vigencia', 'p.dni', 'li.ruc', 'li.inspector', 'li.fechaEmision')
                ->join('razonesociales as ra', 'ra.id', '=', 'li.idrazonsocial')
                ->join('nombrescomerciales as nom', 'nom.id', '=', 'li.idnombreComercial')
                ->join('rubro as ru', 'ru.id', '=', 'li.idrubro')
                ->join('propietario as p', 'p.id', '=', 'li.idpropietario')
                ->where('li.idnombreComercial', $id)
                ->first();
    
            if ($licencia) {
                return response()->json(['success' => true,
                'message' => 'Datos obtenidos correctamente',
                'carnet' => $licencia]);
            } else {
                return response()->json(['error' => 'Licencia no encontrada'], 404);
            }
        } catch (\Exception $e) {
            
            return response()->json(['error' => 'Se produjo un error al expedir la licencia.'], 500);
        }
    }
}
