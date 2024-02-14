<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('constancia', function (Blueprint $table) {
               // Eliminar la restricción de clave foránea
               $table->dropForeign(['idpropietario']);
            
               // Eliminar la columna de clave foránea
               $table->dropColumn('idpropietario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('constancia', function (Blueprint $table) {
            $table->foreignId('idpropietario')->constrained('propietario');
        });
    }
};
