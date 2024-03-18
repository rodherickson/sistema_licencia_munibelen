<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposdeMultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoMultas = [
            ['id' => '1', 'nombreMulta' => 'MULTA POR INFRAESTRUCTURA', 'descripcion' => ''],
            ['id' => '2', 'nombreMulta' => 'MULTA POR ILUMINACION', 'descripcion' => ''],
            ['id' => '3', 'nombreMulta' => 'MULTA POR VENTILACION', 'descripcion' => ''],
            ['id' => '4', 'nombreMulta' => 'MULTA DE BUENAS PRACTICAS DE HIGIENE', 'descripcion' => ''],
            ['id' => '5', 'nombreMulta' => 'MULTA DE BUENAS PRACTICAS DE MANIPULACION', 'descripcion' => ''],
            ['id' => '6', 'nombreMulta' => 'MULTA DE ALMACENAMIENTO DE ALIMENTOS AGROPECUARIOS PRIMARIOS Y PINSOS', 'descripcion' => ''],
            ['id' => '7', 'nombreMulta' => 'MULTA DE VEHICULOS DE TRANSPORTE DE ALIMENTOS AGROPECUARIOS PRIMARIOS Y PIENSOS', 'descripcion' => ''],
            ['id' => '8', 'nombreMulta' => 'MULTA POR CONDICIONES GENERALES DEL VEHICULO', 'descripcion' => ''],
            ['id' => '9', 'nombreMulta' => 'MULTA DE MANIPULADOR', 'descripcion' => ''],
        ];
        DB::table('tipo_multa')->insert($tipoMultas);
    }
}
