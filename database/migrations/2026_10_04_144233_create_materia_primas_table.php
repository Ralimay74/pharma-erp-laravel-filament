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
    Schema::create('materia_primas', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('codigo_interno')->unique();
        $table->string('proveedor')->nullable();
        $table->decimal('stock_actual', 10, 2)->default(0);
        $table->string('unidad_medida'); // ej: kg, L, unidades
        $table->enum('estado', ['Activo', 'Inactivo', 'Cuarentena'])->default('Activo');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materia_primas');
    }
};
