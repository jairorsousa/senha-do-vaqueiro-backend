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
        Schema::table('agendamento', function(Blueprint $table) {
            $table->foreignId('ordem_de_servico_id')->nullable()->references('id')->on('ordem_de_servico');
            $table->date('data_original')->nullable();
            $table->date('periodo_original')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendamento', function(Blueprint $table) {
            $table->dropForeign('ordem_de_servico_id');
            $table->dropColumn('data_original');
            $table->dropColumn('periodo_original');
        });
    }
};
