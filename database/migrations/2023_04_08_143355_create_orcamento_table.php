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
        Schema::create('orcamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_de_servico_id')->references('id')->on('ordem_de_servico');
            $table->foreignId('prestador_servico_id')->references('id')->on('prestador_servico');
            $table->foreignId('atendente_id')->references('id')->on('users');
            $table->foreignId('auditor_id')->references('id')->on('users');
            $table->string('status')->default('AGUARDANDO AUDITORIA');
            $table->dateTime('data_vencimento')->nullable();
            $table->decimal('valor_total_orcamento');
            $table->decimal('valor_total_aprovado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcamento');
    }
};
