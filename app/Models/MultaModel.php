<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultaModel extends Model
{
    use HasFactory;
    protected $table = 'multa';
    public $timestamps = false;
    protected $fillable = 
    ['idlicencia', 
    'idtipoMulta',
    'expiredate'];

 
    
}
