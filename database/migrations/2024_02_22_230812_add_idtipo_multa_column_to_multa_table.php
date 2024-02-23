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
            $table->foreignId('idtipoMulta')
            ->nullable()
            ->after('idlicencia')
            ->constrained('tipo_multa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multa', function (Blueprint $table) {
            $table->dropForeign(['idtipoMulta']);
            $table->dropColumn('idtipoMulta');
        });
    }
};
