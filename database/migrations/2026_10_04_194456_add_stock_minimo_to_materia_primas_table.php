<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materia_primas', function (Blueprint $table) {
            $table->decimal('stock_minimo', 10, 2)->default(50)->after('stock_actual');
        });
    }

    public function down(): void
    {
        Schema::table('materia_primas', function (Blueprint $table) {
            $table->dropColumn('stock_minimo');
        });
    }
};
