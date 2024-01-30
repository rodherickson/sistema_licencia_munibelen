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
            $table->string('nombreempresa',255);
            $table->string('ruc',255);
            $table->string('direccion',255);
            $table->string('area',255);
            $table->string('aforo',255);
            $table->date('fecha_emision');
            $table->date('fecha_caducidad');
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
