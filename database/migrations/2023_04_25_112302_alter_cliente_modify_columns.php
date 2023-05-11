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
        Schema::table('cliente', function(Blueprint $table) {
            $table->foreignId('cidade_id')->nullable()->change();
            $table->string('endereco')->nullable()->change();
            $table->integer('numero')->nullable()->change();
            $table->string('cep')->nullable()->change();
            $table->string('uf',2)->nullable()->change();
            $table->string('bairro')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cliente', function(Blueprint $table) {
            $table->foreignId('cidade_id')->nullable(false)->change();
            $table->string('endereco')->nullable(false)->change();
            $table->integer('numero')->nullable(false)->change();
            $table->string('cep')->nullable(false)->change();
            $table->string('uf',2)->nullable(false)->change();
            $table->string('bairro')->nullable(false)->change();
        });
    }
};
