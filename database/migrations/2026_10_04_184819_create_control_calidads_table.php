<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('control_calidads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_produccion_id')->constrained('lote_produccions')->cascadeOnDelete();
            $table->string('analista');
            $table->date('fecha_analisis')->default(now());
            $table->decimal('ph', 4, 2)->nullable();
            $table->decimal('pureza_porcentaje', 5, 2)->nullable();
            $table->enum('resultado', ['Cumple', 'No Cumple', 'Pendiente'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_calidads');
    }
};
