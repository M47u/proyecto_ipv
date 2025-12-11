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
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vivienda_id')->constrained('viviendas')->onDelete('cascade');
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('asignado_por')->constrained('users')->onDelete('restrict');
            $table->date('fecha_asignacion');
            $table->date('fecha_limite')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'cancelada'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->text('notas')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('inspector_id');
            $table->index('vivienda_id');
            $table->index('estado');
            $table->index('fecha_asignacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
