<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lote_produccions', function (Blueprint $table) {
            $table->id();
            $table->string('numero_lote')->unique();
            $table->foreignId('producto_terminado_id')->constrained('producto_terminados')->cascadeOnDelete();
            $table->date('fecha_fabricacion');
            $table->date('fecha_caducidad');
            $table->decimal('cantidad_producida', 10, 2);
            $table->enum('estado', ['En proceso', 'Liberado', 'Rechazado', 'Cuarentena'])->default('En proceso');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lote_produccions');
    }
};
