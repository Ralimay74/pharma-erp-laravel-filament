<?php

namespace Database\Seeders;

use App\Models\MateriaPrima;
use Illuminate\Database\Seeder;

class MateriaPrimaSeeder extends Seeder
{
    public function run(): void
    {
        $materiasPrimas = [
            [
                'nombre' => 'Paracetamol',
                'codigo_interno' => 'MP-001',
                'proveedor' => 'Bayer HealthCare',
                'stock_actual' => 150.50,
                'unidad_medida' => 'kg',
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'Ibuprofeno',
                'codigo_interno' => 'MP-002',
                'proveedor' => 'BASF Pharma',
                'stock_actual' => 85.00,
                'unidad_medida' => 'kg',
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'Ácido Acetilsalicílico',
                'codigo_interno' => 'MP-003',
                'proveedor' => 'Lonza Group',
                'stock_actual' => 200.75,
                'unidad_medida' => 'kg',
                'estado' => 'Cuarentena',
            ],
            [
                'nombre' => 'Excipiente Celulosa Microcristalina',
                'codigo_interno' => 'MP-004',
                'proveedor' => 'FMC Corporation',
                'stock_actual' => 500.00,
                'unidad_medida' => 'kg',
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'Estearato de Magnesio',
                'codigo_interno' => 'MP-005',
                'proveedor' => 'Merck KGaA',
                'stock_actual' => 45.20,
                'unidad_medida' => 'kg',
                'estado' => 'Inactivo',
            ],
        ];

        foreach ($materiasPrimas as $mp) {
            MateriaPrima::create($mp);
        }
    }
}
