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
        Schema::table('usuario_empresa', function(Blueprint $table) {
            $table->foreignId('seguradora_id')->nullable()->references('seguradora_id')->on('empresa_seguradora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario_empresa', function (Blueprint $table) {
            $table->dropForeign('usuario_empresa_seguradora_id_foreign');
        });
    }
};
