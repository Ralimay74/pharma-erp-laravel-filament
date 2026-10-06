<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nif_cif')->unique();
            $table->enum('tipo', ['Distribuidora', 'Hospital', 'Clínica', 'Farmacia', 'Laboratorio', 'Otro'])->default('Distribuidora');
            $table->string('contacto')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('pais')->default('España');
            $table->string('codigo_postal')->nullable();
            $table->decimal('limite_credito', 10, 2)->default(0);
            $table->enum('estado', ['Activo', 'Inactivo', 'Moroso'])->default('Activo');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
