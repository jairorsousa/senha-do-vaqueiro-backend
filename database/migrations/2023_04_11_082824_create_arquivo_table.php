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
        Schema::create('arquivo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ordem_de_servico_id')->references('id')->on('ordem_de_servico');
            $table->string('nome_arquivo');
            $table->string('alias')->nullable();
            $table->string('formato');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arquivo');
    }
};
