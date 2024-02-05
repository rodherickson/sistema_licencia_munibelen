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
        Schema::create('detalle_multa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idmulta')->constrained('multa');
            $table->date('fecha');
            $table->enum('status', ['Abierto', 'En Proceso', 'Finalizado', 'Cancelado'])->default('Abierto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_multa');
    }
};
