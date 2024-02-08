<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Propietario extends Model
{    
     public $timestamps = false;
    protected $table = 'propietario'; 
    protected $fillable = ['nombre', 
    'apellidos', 
    'dni', 
    'celular', 
    'correo', 
    'direccion',
    'distrito'
];
    use HasFactory;
    public static function obtenerTodos()
    {
        return self::all();
    }
    
    public static function obtenerPorId($propietarioId)
    {
        return self::find($propietarioId);
    }

    public static function actualizarDatos($propietarioId, $datos)
    {
        $propietario = self::find($propietarioId);
        if ($propietario) {
            $propietario->update($datos);
            return $propietario;
        }

        return null; // O manejar de otra manera si no se encuentra el propietario
    }

}
