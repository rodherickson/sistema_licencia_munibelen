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
        Schema::table('constancia', function (Blueprint $table) {
            
            $table->date('fechaEmision');
            $table->date('fechaCaducidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('constancia', function (Blueprint $table) {
            $table->dropColumn('fechaEmision');
            $table->dropColumn('fechaCaducidad');
        });
    }
};
