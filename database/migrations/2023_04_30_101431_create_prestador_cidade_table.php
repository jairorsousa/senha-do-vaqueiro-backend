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
        Schema::create('prestador_cidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->references('id')->on('cidades');
            $table->foreignId('prestador_de_servico_id')->references('id')->on('prestador_servico');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestador_cidade');
    }
};
