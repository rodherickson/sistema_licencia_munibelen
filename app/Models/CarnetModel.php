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
        'ubicacion', 
        'cuadra', 
        'largo', 
        'ancho',
        'n_mesa',
        'categoria',
        'fecha_emision',
        'fecha_caducidad'
    ];

  
}
