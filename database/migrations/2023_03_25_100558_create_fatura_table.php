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
        Schema::create('fatura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->references('id')->on('empresas');
            $table->string('status');
            $table->decimal('valor_nominal');
            $table->date('data_vencimento');
            $table->decimal('valor_juros')->nullable();
            $table->decimal('valor_multa')->nullable();
            $table->decimal('valor_pago')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->string('forma_pagamento')->nullable();
            $table->decimal('acrescimo')->nullable();
            $table->decimal('desconto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura');
    }
};
