<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Propietario extends Model
{    
    use HasFactory;
    public $timestamps = false;
    protected $table = 'propietario'; 
    protected $fillable = 
    ['nombre', 
    'apellidos', 
    'dni',
    'celular', 
    'correo', 
    'direccion',
    'distrito'
];
   
    
}
