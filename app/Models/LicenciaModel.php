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
        'idrubro',
        'idrazonSocial', 
        'idnombreComercial',
        'ruc', 
        'direccionEstablecimiento', 
        'distritoEstablecimiento',
        'area', 
        'inspector', 
        'aforo',
        'fechaEmision',
         'fechaCaducidad'
    ];



}
