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
        Schema::create('licencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idpropietario')->constrained('propietario');
            $table->foreignId('idrubro')->constrained('rubro');
            // $table->string('razonSocial',255);
            $table->foreignId('idrazonsocial')->constrained('razonesociales');
            $table->foreignId('idnombreComercial')->constrained('nombrescomerciales');
            // $table->string('nombreComercial',255);
            $table->string('ruc',255);
            $table->string('direccionEstablecimiento',255);
            $table->string('distritoEstablecimiento',255);
            $table->string('area',255);//decimal que tenga 2 decimales
            $table->string('aforo',255);
            $table->string('inspector',255);
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
        Schema::dropIfExists('licencia');
    }
};
