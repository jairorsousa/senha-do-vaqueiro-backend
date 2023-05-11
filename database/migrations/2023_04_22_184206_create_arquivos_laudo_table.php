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
        Schema::create('arquivos_laudo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laudo_id')->references('id')->on('laudo');
            $table->foreignId('arquivo_id')->references('id')->on('arquivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arquivos_laudo');
    }
};
