<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RubrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rubros = [
            ['id' => '1', 'nombre_rubro' => 'Abarrotes', 'descripcion' => ''],
            ['id' => '2', 'nombre_rubro' => 'Artesanías', 'descripcion' => ''],
            ['id' => '3', 'nombre_rubro' => 'Carnes', 'descripcion' => ''],
            ['id' => '4', 'nombre_rubro' => 'Carnes Rojas', 'descripcion' => ''],
            ['id' => '5', 'nombre_rubro' => 'Carne de Cerdo', 'descripcion' => ''],
            ['id' => '6', 'nombre_rubro' => 'Cecina', 'descripcion' => ''],
            ['id' => '7', 'nombre_rubro' => 'Chonta', 'descripcion' => ''],
            ['id' => '8', 'nombre_rubro' => 'Comidas', 'descripcion' => ''],
            ['id' => '9', 'nombre_rubro' => 'Corteza y Artesanías', 'descripcion' => ''],
            ['id' => '10', 'nombre_rubro' => 'Desayuno', 'descripcion' => ''],
            ['id' => '11', 'nombre_rubro' => 'Fariña y Tapioca', 'descripcion' => ''],
            ['id' => '12', 'nombre_rubro' => 'Frutas de la Región', 'descripcion' => ''],
            ['id' => '13', 'nombre_rubro' => 'Gallinas Visceradas', 'descripcion' => ''],
            ['id' => '14', 'nombre_rubro' => 'Jugueterías y Comidas', 'descripcion' => ''],
            ['id' => '15', 'nombre_rubro' => 'Kiosko', 'descripcion' => ''],
            ['id' => '16', 'nombre_rubro' => 'Licores', 'descripcion' => ''],
            ['id' => '17', 'nombre_rubro' => 'Licores-Plantas Medic.', 'descripcion' => ''],
            ['id' => '18', 'nombre_rubro' => 'Mondongo y Vísceras', 'descripcion' => ''],
            ['id' => '19', 'nombre_rubro' => 'Panes', 'descripcion' => ''],
            ['id' => '20', 'nombre_rubro' => 'Pescado Paiche', 'descripcion' => ''],
            ['id' => '21', 'nombre_rubro' => 'Pescados Dorado', 'descripcion' => ''],
            ['id' => '22', 'nombre_rubro' => 'Pescados y Mariscos', 'descripcion' => ''],
            ['id' => '23', 'nombre_rubro' => 'Plantas Medicinales', 'descripcion' => ''],
            ['id' => '24', 'nombre_rubro' => 'Ponche', 'descripcion' => ''],
            ['id' => '25', 'nombre_rubro' => 'Productos No Alimenticios', 'descripcion' => ''],
            ['id' => '26', 'nombre_rubro' => 'Variedades', 'descripcion' => ''],
            ['id' => '27', 'nombre_rubro' => 'Verduras', 'descripcion' => ''],
            ['id' => '28', 'nombre_rubro' => 'Verduras y Frutas', 'descripcion' => '']
        ];

        
        DB::table('rubro')->insert($rubros);
    
    }

    }


