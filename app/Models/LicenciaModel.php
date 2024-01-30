<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenciaModel extends Model
{
    public $timestamps = false;
    protected $table = 'licencia';
    use HasFactory;

    protected $fillable = [
        'idpropietario', 
        'nombreempresa', 
        'ruc', 
        'direccion', 
        'area', 
        'aforo',
        'fecha_emision',
        'fecha_caducidad'
    ];

}
