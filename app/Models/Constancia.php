<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Constancia extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'constancia';
    protected $fillable = [
        'fechaEmision',
        'fechaCaducidad',
        'idpropietario' 
    ];

}
