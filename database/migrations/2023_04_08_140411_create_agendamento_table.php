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
        Schema::create('agendamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestador_servico_id')->references('id')->on('prestador_servico');
            $table->foreignId('usuario_empresa_id')->references('id')->on('usuario_empresa');
            $table->date('data');
            $table->enum('periodo', ['MANHA','TARDE','NOITE']);
            $table->string('status')->default('EM DIAS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamento');
    }
};
