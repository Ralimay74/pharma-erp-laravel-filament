<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materia_primas', function (Blueprint $table) {
            $table->renameColumn('proveedor', 'nombre_proveedor_legacy');
        });
    }

    public function down(): void
    {
        Schema::table('materia_primas', function (Blueprint $table) {
            $table->renameColumn('nombre_proveedor_legacy', 'proveedor');
        });
    }
};
