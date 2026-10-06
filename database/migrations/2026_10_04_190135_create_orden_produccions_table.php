<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_produccions', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden')->unique();
            $table->foreignId('producto_terminado_id')->constrained('producto_terminados')->cascadeOnDelete();
            $table->decimal('cantidad_planificada', 10, 2);
            $table->date('fecha_planificada');
            $table->enum('estado', ['Pendiente', 'En Producción', 'Completada', 'Cancelada'])->default('Pendiente');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_produccions');
    }
};
