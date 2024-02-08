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
        Schema::create('propietario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',255);
            $table->string('apellidos',255);
            $table->string('dni', 8)->unique();
            $table->string('celular',255);
            $table->string('correo',255);
            $table->string('direccion',255);
            $table->string('distrito',255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propietario');
    }
};
