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
        Schema::table('cliente',function(Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('telefone2')->nullable();
            $table->string('contato')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cliente',function(Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('telefone');
            $table->dropColumn('telefone2');
            $table->dropColumn('contato');
        });
    }
};
