<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formula_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_terminado_id')->constrained('producto_terminados')->cascadeOnDelete();
            $table->foreignId('materia_prima_id')->constrained('materia_primas')->cascadeOnDelete();
            $table->decimal('cantidad_requerida', 10, 2);
            $table->string('unidad_medida');
            $table->unique(['producto_terminado_id', 'materia_prima_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_items');
    }
};
