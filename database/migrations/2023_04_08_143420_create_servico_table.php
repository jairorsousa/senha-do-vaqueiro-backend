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
        Schema::create('servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orcamento_id')->references('id')->on('orcamento');
            $table->enum('tipo_servico',['VISITA', 'MÃO DE OBRA', 'DESLOCAMENTO', 'PEÇAS', 'OUTROS']);
            $table->text('descricao');
            $table->string('observacao')->nullable();
            $table->integer('quantidade');
            $table->decimal('valor_solicitado');
            $table->decimal('valor_aprovado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servico');
    }
};
