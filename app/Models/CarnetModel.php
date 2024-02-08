<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarnetModel extends Model
{   
    use HasFactory;
    
    public $timestamps = false;
    protected $table = 'carnet';

    protected $fillable = [
        'idpropietario', 
        'idrubro', 
        'lugarEstablecimiento', 
        'cuadra', 
        'largo', 
        'ancho',
        'nroMesa',
        'categoria',
        'fechaEmision',
        'fechaCaducidad'
    ];

  
}
