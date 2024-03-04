<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TipoMultaModel extends Model
{   protected $table = 'tipo_multa';
    public $timestamps = false;
    protected $fillable = ['nombreMulta', 
    'descripcion'];
    use HasFactory;


    public function listTipoMulta(){

        try{
    
            $tiposMultaFromDB = TipoMultaModel::select('id', 'nombreMulta')->get();
    
            // Inicializamos un array vacío para almacenar los tipos de multa formateados
            $tiposMultas = [];
    
            // Iteramos sobre los resultados de la consulta
            foreach ($tiposMultaFromDB as $tipoMulta) {
    
                // Formateamos cada tipo de multa y lo agregamos al array
                $tiposMultas[] = [
                    'value' => $tipoMulta->id,
                    'label' => $tipoMulta->nombreMulta,
                ];
            }
    
            return $tiposMultas;
    
        } catch(\Exception $e){
    
            throw new \Exception('Error al obtener los Tipos de Multas: ' . $e->getMessage());
    
        }
    
    } 

}
