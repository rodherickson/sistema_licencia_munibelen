<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class Carnet_files extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'carnet_files';
    protected $fillable = [
        'id_carnet_files',
        'original_name',
        'unique_name',
        'type_file',
        'path_file',
        'date_create'
    ];

    public static function saveFiles($id, $filename, $unique_name, $extension, $path)
    {
        Carnet_files::create([
            'id_convocatoria_files' => $id,
            'original_name' => $filename,
            'unique_name' => $unique_name,
            'type_file' => $extension,
            'path_file' => $path,
            'date_create' => date('Y-m-d')
        ]);
    }

    public static function deleteFile($id)
    {
        try 
        {
            $file = Carnet_files::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Archivo con el ID `$id` no encontrado", 404);
        }
          
          DB::table('convocatoria_files')->where('id', '=', $id)->delete();
    
          return $pathFileBanner = $file->path_file;
    }


    public static function deleteGroupFile($id){ 
        try {
            $file = DB::table('convocatoria_files')
                ->where('id_convocatoria_files', '=', $id)
                ->get();
        
            $file = $file->toArray(); 
        
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Archivo con el ID `$id` no encontrado", 404);
        }
        
        DB::table('convocatoria_files')->where('id_convocatoria_files', '=', $id)->delete();
        
        return $pathFileBanner = $file;        
    }
 

}
