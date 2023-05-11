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
        Schema::create('ordem_de_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->references('id')->on('cliente');
            $table->foreignId('produto_id')->references('id')->on('produto');
            $table->foreignId('atendente_id')->references('id')->on('users');
            $table->foreignId('direcionador_id')->references('id')->on('users');
            $table->foreignId('status_os_id')->references('id')->on('status_os');
            $table->integer('numero_os');
            $table->text('sinistro');
            $table->dateTime('data_abertura');
            $table->dateTime('data_fechamento')->nullable();
            $table->enum('tipo_atendimento', ['NORMAL','PRIORITÁRIO','EMERGENCIAL']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordem_de_servico');
    }
};
