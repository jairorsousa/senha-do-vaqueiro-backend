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
        Schema::create('prestador_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->references('id')->on('empresas');
            $table->string('nome');
            $table->string('cpf_cnpj');
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('uf',2)->nullable();
            $table->string('cidade')->nullable();
            $table->string('cep')->nullable();
            $table->boolean('status')->nullable();
            $table->text('chave_pix')->nullable();
            $table->string('banco')->nullable();
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestador_servico');
    }
};
