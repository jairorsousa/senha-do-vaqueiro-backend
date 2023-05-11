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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fantasia');
            $table->string('razao_social');
            $table->string('cnpj')->unique();
            $table->string('inscricao_estadual')->nullable();
            $table->string('telefone');
            $table->string('celular');
            $table->boolean('status')->default(true);
            $table->string('cep');
            $table->string('endereco')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
