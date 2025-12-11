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
        Schema::create('inspeccion_fallas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspeccion_id')->constrained('inspecciones')->onDelete('cascade');
            $table->enum('categoria', ['estructura', 'electrica', 'sanitaria', 'gas', 'pintura', 'aberturas', 'pisos', 'otras']);
            $table->text('descripcion');
            $table->enum('gravedad', ['leve', 'moderada', 'grave', 'critica']);
            $table->string('ubicacion', 255)->nullable()->comment('Ej: cocina, baño principal');
            $table->boolean('requiere_accion_inmediata')->default(false);
            $table->timestamp('created_at')->useCurrent();
            
            // Índices
            $table->index('inspeccion_id');
            $table->index('categoria');
            $table->index('gravedad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeccion_fallas');
    }
};
