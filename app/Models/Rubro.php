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

}
