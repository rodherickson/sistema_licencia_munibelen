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
        Schema::table('files_multas', function (Blueprint $table) {
            $table->foreignId('iduser')->nullable()->constrained('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files_multas', function (Blueprint $table) {
            $table->dropForeign(['iduser']);
            $table->dropColumn('iduser');
        });
    }
};
