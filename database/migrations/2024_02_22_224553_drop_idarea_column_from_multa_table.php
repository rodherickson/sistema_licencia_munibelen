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
        Schema::table('multa', function (Blueprint $table) {
            $table->dropForeign(['idarea']); // Eliminar la clave externa primero si existe
            $table->dropColumn('idarea');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multa', function (Blueprint $table) {
            $table->foreignId('idarea')->constrained('area');
        });
    }
};
