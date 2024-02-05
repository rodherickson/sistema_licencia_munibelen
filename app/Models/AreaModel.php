<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AreaModel extends Model
{
    protected $table = 'area';
    public $timestamps = false;
    protected $fillable = ['nombre_area', 
    'descripcion'];
    use HasFactory;

    public static function listArea(){
        $area=DB::table('area')
        ->get();
        return $area;
    }
}
