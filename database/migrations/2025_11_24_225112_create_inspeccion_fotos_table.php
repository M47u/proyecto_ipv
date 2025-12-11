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
        Schema::create('inspeccion_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspeccion_id')->constrained('inspecciones')->onDelete('cascade');
            $table->string('ruta_archivo', 500);
            $table->string('nombre_original', 255);
            $table->enum('tipo_foto', ['general', 'estructura', 'instalaciones', 'detalle_falla', 'otra']);
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamp('created_at')->useCurrent();
            
            // Índices
            $table->index('inspeccion_id');
            $table->index('tipo_foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspeccion_fotos');
    }
};
