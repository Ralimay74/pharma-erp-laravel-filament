<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materia_primas', function (Blueprint $table) {
            $table->foreignId('proveedor_id')->nullable()->after('proveedor')->constrained('proveedors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('materia_primas', function (Blueprint $table) {
            $table->dropForeign(['proveedor_id']);
            $table->dropColumn('proveedor_id');
        });
    }
};
