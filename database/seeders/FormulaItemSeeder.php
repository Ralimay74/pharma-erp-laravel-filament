<?php

namespace Database\Seeders;

use App\Models\FormulaItem;
use App\Models\MateriaPrima;
use App\Models\ProductoTerminado;
use Illuminate\Database\Seeder;

class FormulaItemSeeder extends Seeder
{
    public function run(): void
    {
        $paracetamol = ProductoTerminado::where('codigo_producto', 'PT-001')->first();
        $ibuprofeno = ProductoTerminado::where('codigo_producto', 'PT-002')->first();

        $mpParacetamol = MateriaPrima::where('codigo_interno', 'MP-001')->first();
        $mpIbuprofeno = MateriaPrima::where('codigo_interno', 'MP-002')->first();
        $mpCelulosa = MateriaPrima::where('codigo_interno', 'MP-004')->first();
        $mpEstearato = MateriaPrima::where('codigo_interno', 'MP-005')->first();

        if ($paracetamol && $mpParacetamol) {
            FormulaItem::create([
                'producto_terminado_id' => $paracetamol->id,
                'materia_prima_id' => $mpParacetamol->id,
                'cantidad_requerida' => 500,
                'unidad_medida' => 'mg',
            ]);
        }

        if ($paracetamol && $mpCelulosa) {
            FormulaItem::create([
                'producto_terminado_id' => $paracetamol->id,
                'materia_prima_id' => $mpCelulosa->id,
                'cantidad_requerida' => 50,
                'unidad_medida' => 'mg',
            ]);
        }

        if ($paracetamol && $mpEstearato) {
            FormulaItem::create([
                'producto_terminado_id' => $paracetamol->id,
                'materia_prima_id' => $mpEstearato->id,
                'cantidad_requerida' => 5,
                'unidad_medida' => 'mg',
            ]);
        }

        if ($ibuprofeno && $mpIbuprofeno) {
            FormulaItem::create([
                'producto_terminado_id' => $ibuprofeno->id,
                'materia_prima_id' => $mpIbuprofeno->id,
                'cantidad_requerida' => 400,
                'unidad_medida' => 'mg',
            ]);
        }

        if ($ibuprofeno && $mpCelulosa) {
            FormulaItem::create([
                'producto_terminado_id' => $ibuprofeno->id,
                'materia_prima_id' => $mpCelulosa->id,
                'cantidad_requerida' => 80,
                'unidad_medida' => 'mg',
            ]);
        }
    }
}
