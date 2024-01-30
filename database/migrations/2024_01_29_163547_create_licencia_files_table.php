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
        Schema::create('licencia_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_licencia_files')->constrained('licencia');
            $table->string('original_name', 700);
            $table->string('unique_name', 18);
            $table->string('type_file', 8);
            $table->string('path_file', 300);
            $table->dateTime('date_create');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licencia_files');
    }
};
