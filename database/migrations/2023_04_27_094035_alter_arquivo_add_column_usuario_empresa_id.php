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
        Schema::table('arquivo', function(Blueprint $table) {
            $table->foreignId('usuario_empresa_id')->nullable()->references('id')->on('usuario_empresa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arquivo', function(Blueprint $table) {
            $table->dropForeign('usuario_empresa_id');
        });
    }
};
