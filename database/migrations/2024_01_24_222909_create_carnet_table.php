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
        Schema::create('carnet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idpropietario')->constrained('propietario');
            $table->foreignId('idrubro')->constrained('rubro');
            $table->string('lugarEstablecimiento',255);
            $table->string('cuadra',255);
            $table->string('largo',255);
            $table->string('ancho',255);
            $table->string('nroMesa',255);
            $table->string('categoria',255);
            $table->date('fechaEmision');
            $table->date('fechaCaducidad');
            $table->enum('estado', ['Abierto', 'Expedido', 'Caducado'])->default('Abierto');





            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carnet');
    }
};
