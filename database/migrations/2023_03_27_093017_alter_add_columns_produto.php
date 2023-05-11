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
        Schema::table('produto',function(Blueprint $table) {
            $table->foreignId('cliente_id')->nullable()->references('id')->on('cliente');
            $table->text('numero_serie')->nullable();
            $table->string('status')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_nf')->nullable();
            $table->string('imei')->nullable();
            $table->boolean('garantia_fabrica')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produto',function(Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn('cliente_id');
            $table->dropColumn('numero_serie');
            $table->dropColumn('status');
            $table->dropColumn('modelo');
            $table->dropColumn('numero_nf');
            $table->dropColumn('imei');
            $table->dropColumn('garantia_fabrica');
        });
    }
};
