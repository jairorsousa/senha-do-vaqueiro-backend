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
        Schema::table('orcamento', function(Blueprint $table) {
            $table->date('data_criacao')->nullable();
            $table->date('data_auditacao')->nullable();
            $table->date('data_envio_orcamento')->nullable();
            $table->date('data_retorno_orcamento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orcamento', function(Blueprint $table) {
            $table->dropColumn('data_criacao');
            $table->dropColumn('data_auditacao');
            $table->dropColumn('data_envio_orcamento');
            $table->dropColumn('data_retorno_orcamento');
        });
    }
};
