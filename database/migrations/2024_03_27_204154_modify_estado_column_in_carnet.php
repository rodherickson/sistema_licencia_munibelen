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
        Schema::table('carnet', function (Blueprint $table) {
            $table->enum('estado', ['Abierto', 'Expedido', 'Por Caducar', 'Caducado'])->default('Abierto')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carnet', function (Blueprint $table) {
            $table->enum('estado', ['Abierto', 'Expedido', 'Caducado'])->default('Abierto')->change();
        });
    }
};
