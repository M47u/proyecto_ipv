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
        Schema::create('viviendas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('direccion', 255);
            $table->string('barrio', 100)->nullable();
            $table->string('ciudad', 100)->default('Formosa');
            $table->string('provincia', 100)->default('Formosa');
            $table->enum('tipo_vivienda', ['proxima_entrega', 'entregada', 'recuperada']);
            $table->decimal('superficie_cubierta', 8, 2)->nullable();
            $table->decimal('superficie_terreno', 8, 2)->nullable();
            $table->integer('cantidad_ambientes')->nullable();
            $table->string('propietario_actual', 255)->nullable();
            $table->string('telefono_contacto', 20)->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['activa', 'inactiva'])->default('activa');
            $table->timestamps();
            
            // Índices
            $table->index('codigo');
            $table->index('tipo_vivienda');
            $table->index('estado');
            $table->index('ciudad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viviendas');
    }
};
