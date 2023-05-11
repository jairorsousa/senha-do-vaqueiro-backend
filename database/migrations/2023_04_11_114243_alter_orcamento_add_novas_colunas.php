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
            $table->enum('tipo_orcamento', ['NORMAL', 'AVULSO'])->nullable();
            $table->string('nome_favorecido')->nullable();
            $table->string('nome')->nullable();
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->string('chave_pix')->nullable();
            $table->foreignId('auditor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orcamento', function(Blueprint $table) {
            $table->dropColumn('tipo_orcamento', ['NORMAL', 'AVULSO']);
            $table->dropColumn('nome_favorecido');
            $table->dropColumn('nome');
            $table->dropColumn('agencia');
            $table->dropColumn('conta');
            $table->dropColumn('chave_pix');
            $table->foreignId('auditor_id')->nullable(false)->change();
        });
    }
};
