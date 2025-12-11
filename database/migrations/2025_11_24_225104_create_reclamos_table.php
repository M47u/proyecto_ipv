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
        Schema::create('reclamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vivienda_id')->constrained('viviendas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('inspeccion_id')->nullable()->constrained('inspecciones')->onDelete('set null');
            $table->string('titulo', 255);
            $table->string('reclamante_nombre', 255)->nullable();
            $table->string('reclamante_telefono', 20)->nullable();
            $table->string('reclamante_email', 255)->nullable();
            $table->dateTime('fecha_reclamo')->nullable();
            $table->enum('tipo_reclamo', ['estructural', 'instalaciones', 'humedad', 'filtraciones', 'aberturas', 'otros'])->nullable();
            $table->text('descripcion');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('estado', ['pendiente', 'en_proceso', 'resuelto', 'rechazado'])->default('pendiente');
            $table->dateTime('fecha_resolucion')->nullable();
            $table->text('notas_resolucion')->nullable();
            $table->timestamps();

            // Índices
            $table->index('vivienda_id');
            $table->index('user_id');
            $table->index('inspeccion_id');
            $table->index('fecha_reclamo');
            $table->index('estado');
            $table->index('prioridad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclamos');
    }
};
