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
        Schema::create('multa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idlicencia')->constrained('licencia');
            $table->foreignId('idtipo_multa')->constrained('tipo_multa');
            $table->foreignId('idarea')->constrained('area');
            $table->date('expiredate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multa');
    }
};
