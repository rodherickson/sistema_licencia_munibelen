<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazonesocialesModel extends Model
{
    protected $table = 'razonesociales';
    public $timestamps = false;
    protected $fillable = ['razonSocial']; // Solo necesitas tener el campo 'razonSocial' aquí

    use HasFactory;
}
