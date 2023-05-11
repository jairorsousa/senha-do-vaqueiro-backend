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
        Schema::create('evento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->references('id')->on('cliente');
            $table->foreignId('ordem_de_servico_id')->references('id')->on('ordem_de_servico');
            $table->foreignId('motivo_evento_id')->references('id')->on('motivo_evento');
            $table->foreignId('usuario_empresa_id')->references('id')->on('usuario_empresa');
            $table->text('descricao');
            $table->string('observacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento');
    }
};
