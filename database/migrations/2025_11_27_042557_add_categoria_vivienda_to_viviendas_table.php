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
        Schema::table('viviendas', function (Blueprint $table) {
            $table->enum('categoria_vivienda', ['37m2(c5)', 'F', 'F DIS', 'H', 'C'])
                  ->nullable()
                  ->after('tipo_vivienda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viviendas', function (Blueprint $table) {
            $table->dropColumn('categoria_vivienda');
        });
    }
};
