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
        Schema::create('laudo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_de_servico_id')->references('id')->on('ordem_de_servico');
            $table->foreignId('prestador_de_servico_id')->references('id')->on('prestador_servico');
            $table->text('avaliacao');
            $table->longText('assinatura_cliente')->nullable();
            $table->longText('assinatura_prestador')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laudo');
    }
};
