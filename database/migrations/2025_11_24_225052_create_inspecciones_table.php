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
        Schema::create('inspecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vivienda_id')->constrained('viviendas')->onDelete('cascade');
            $table->foreignId('inspector_id')->constrained('users')->onDelete('restrict');
            $table->enum('tipo_inspeccion', ['inicial', 'seguimiento', 'reclamo', 'pre_entrega', 'final']);
            $table->dateTime('fecha_inspeccion');
            $table->enum('estado_general', ['excelente', 'bueno', 'regular', 'malo', 'critico']);
            $table->boolean('es_habitable')->default(true);
            
            // Geolocalización
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->integer('precision_gps')->nullable()->comment('Precisión en metros');
            
            // Evaluación por áreas
            $table->enum('estado_estructura', ['excelente', 'bueno', 'regular', 'malo', 'critico', 'no_aplica'])->nullable();
            $table->enum('estado_instalacion_electrica', ['excelente', 'bueno', 'regular', 'malo', 'critico', 'no_aplica'])->nullable();
            $table->enum('estado_instalacion_sanitaria', ['excelente', 'bueno', 'regular', 'malo', 'critico', 'no_aplica'])->nullable();
            $table->enum('estado_instalacion_gas', ['excelente', 'bueno', 'regular', 'malo', 'critico', 'no_aplica'])->nullable();
            $table->enum('estado_pintura', ['excelente', 'bueno', 'regular', 'malo', 'critico', 'no_aplica'])->nullable();
            $table->enum('estado_aberturas', ['excelente', 'bueno', 'regular', 'malo', 'critico', 'no_aplica'])->nullable();
            $table->enum('estado_pisos', ['excelente', 'bueno', 'regular', 'malo', 'critico', 'no_aplica'])->nullable();
            
            $table->text('observaciones')->nullable();
            $table->text('conclusiones')->nullable();
            $table->boolean('requiere_seguimiento')->default(false);
            $table->date('fecha_proximo_seguimiento')->nullable();
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->timestamps();
            
            // Índices
            $table->index('vivienda_id');
            $table->index('inspector_id');
            $table->index('fecha_inspeccion');
            $table->index('tipo_inspeccion');
            $table->index(['latitud', 'longitud']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspecciones');
    }
};
