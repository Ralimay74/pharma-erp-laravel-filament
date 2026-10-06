<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_terminados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo_producto')->unique();
            $table->string('concentracion');
            $table->string('presentacion');
            $table->string('forma_farmaceutica');
            $table->decimal('precio_venta', 10, 2)->nullable();
            $table->enum('estado', ['En desarrollo', 'Aprobado', 'Suspendido'])->default('En desarrollo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_terminados');
    }
};
