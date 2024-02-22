<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NombrecomercialModel extends Model
{
    use HasFactory;
    protected $table = 'nombrescomerciales';
    public $timestamps = false;
    protected $fillable = ['nombreComercial']; 

   

}

