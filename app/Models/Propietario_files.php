<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class Propietario_files extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'propietario_files';

    protected $fillable = [
        'id_propietario_files',
        'original_name',
        'unique_name',
        'type_file',
        'path_file',
        'date_create'
    ];

    public static function saveFiles($id, $filename, $unique_name, $extension, $path)
    {
        Propietario_files::create([
            'id_propietario_files' => $id,
            'original_name' => $filename,
            'unique_name' => $unique_name,
            'type_file' => $extension,
            'path_file' => $path,
            'date_create' => date('Y-m-d')
        ]);
    }
}
