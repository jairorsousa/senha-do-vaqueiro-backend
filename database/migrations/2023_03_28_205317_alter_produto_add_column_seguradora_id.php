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
        Schema::table('produto', function(Blueprint $table) {
            $table->foreignId('seguradora_id')->nullable()->references('id')->on('seguradora');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto', function(Blueprint $table) {
            $table->dropForeign(['seguradora_id']);
            $table->dropColumn('seguradora_id');
        });
    }
};
