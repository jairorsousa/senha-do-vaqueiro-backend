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
        Schema::create('produto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_id')->references('id')->on('marcas');
            $table->foreignId('revenda_id')->references('id')->on('revendas');
            $table->foreignId('linha_id')->references('id')->on('linha');
            $table->string('nome');
            $table->decimal('valor_custo');
            $table->decimal('valor_bruto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto');
    }
};
