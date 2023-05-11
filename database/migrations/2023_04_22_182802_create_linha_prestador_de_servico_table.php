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
        Schema::create('linha_prestador_de_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('linha_id')->references('id')->on('linha');
            $table->foreignId('prestador_de_servico_id')->references('id')->on('prestador_servico');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linha_prestador_de_servico');
    }
};
