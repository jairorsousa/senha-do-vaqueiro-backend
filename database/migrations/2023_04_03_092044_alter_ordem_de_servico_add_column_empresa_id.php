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
        Schema::table('ordem_de_servico', function(Blueprint $table) {
            $table->foreignId('empresa_id')->nullable()->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordem_de_servico', function(Blueprint $table) {
            $table->dropForeign('empresa_id');
        });
    }
};
