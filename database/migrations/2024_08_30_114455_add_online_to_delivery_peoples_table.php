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
        Schema::table('delivery_peoples', function (Blueprint $table) {
            // Adiciona a coluna online com valor padrão como false
            $table->boolean('online')->default(false)->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_peoples', function (Blueprint $table) {
            // Remove a coluna online se a migração for revertida
            $table->dropColumn('online');
        });
    }
};
