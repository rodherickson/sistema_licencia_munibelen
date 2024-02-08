<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NombrecomercialModel extends Model
{
    protected $table = 'nombrescomerciales';
    public $timestamps = false;
    protected $fillable = ['nombreComercial']; // Solo necesitas tener el campo 'nombreComercial' aquí

    use HasFactory;
}

