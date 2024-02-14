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
        Schema::table('licencia', function (Blueprint $table) {
            $table->dropColumn('fechaCaducidad'); // Eliminar la columna existente

            // Agregar la nueva columna 'vigencia' de tipo entero
            $table->integer('vigencia')->after('fechaEmision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licencia', function (Blueprint $table) {
            $table->dropColumn('vigencia'); // Eliminar la columna 'vigencia' al revertir la migración

            // Agregar nuevamente la columna 'fechaCaducidad' de tipo date
            $table->date('fechaCaducidad')->after('fechaEmision');
        });
    }
};
