<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_MultaModel extends Model
{
    use HasFactory;
    protected $table = 'detalle_multa';
    public $timestamps = false;
    protected $fillable = 
    ['idmulta', 
    'fecha',
     'estatus'];
}
