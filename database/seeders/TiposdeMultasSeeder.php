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
            ['id' => '1', 'nombreMulta' => 'Multas Por Ocupación Indebida De La Vía Pública', 'descripcion' => ''],
            ['id' => '2', 'nombreMulta' => 'Multas Por Incumplimiento De Normativas De Salud e Higiene', 'descripcion' => ''],
            ['id' => '3', 'nombreMulta' => 'Multas Por no contar con permisos o Licencias', 'descripcion' => ''],
        ];
        DB::table('tipo_multa')->insert($tipoMultas);
    }
}
