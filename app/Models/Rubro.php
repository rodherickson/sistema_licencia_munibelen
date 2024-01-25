<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rubro extends Model
{
    protected $table = 'rubro';
    public $timestamps = false;
    protected $fillable = ['nombre_rubro', 
    'descripcion', 
    'estado',];
    use HasFactory;



    public static function listRubro(){
        $rubro=DB::table('rubro')
        ->get();
        return $rubro;
    }

    public function carnets()
    {
        return $this->hasMany(CarnetModel::class, 'id_rubro');
    }
}
