<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE detalle_multa MODIFY COLUMN status ENUM('En Proceso', 'Finalizado', 'Subsanado') NOT NULL DEFAULT 'En Proceso'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
