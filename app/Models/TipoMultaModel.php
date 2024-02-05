<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoMultaModel extends Model
{   protected $table = 'tipo_multa';
    public $timestamps = false;
    protected $fillable = ['nombre_multa', 
    'descripcion'];
    use HasFactory;

    public static function listTipoMulta(){
        $tipo_multa=DB::table('tipo_multa')
        ->get();
        return $tipo_multa;
    }
}
