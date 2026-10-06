<?php

namespace Database\Seeders;

use App\Models\ProductoTerminado;
use Illuminate\Database\Seeder;

class ProductoTerminadoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Paracetamol Genérico',
                'codigo_producto' => 'PT-001',
                'concentracion' => '500mg',
                'forma_farmaceutica' => 'Tableta',
                'presentacion' => 'Blíster 20 unidades',
                'precio_venta' => 2.50,
                'estado' => 'Aprobado',
            ],
            [
                'nombre' => 'Ibuprofeno Plus',
                'codigo_producto' => 'PT-002',
                'concentracion' => '400mg',
                'forma_farmaceutica' => 'Cápsula',
                'presentacion' => 'Blíster 10 unidades',
                'precio_venta' => 3.75,
                'estado' => 'Aprobado',
            ],
            [
                'nombre' => 'Amoxicilina 500',
                'codigo_producto' => 'PT-003',
                'concentracion' => '500mg',
                'forma_farmaceutica' => 'Cápsula',
                'presentacion' => 'Frasco 100ml',
                'precio_venta' => 8.90,
                'estado' => 'En desarrollo',
            ],
            [
                'nombre' => 'Jarabe Expectorante',
                'codigo_producto' => 'PT-004',
                'concentracion' => '250mg/5ml',
                'forma_farmaceutica' => 'Jarabe',
                'presentacion' => 'Frasco 100ml',
                'precio_venta' => 5.20,
                'estado' => 'Aprobado',
            ],
            [
                'nombre' => 'Crema Antiinflamatoria',
                'codigo_producto' => 'PT-005',
                'concentracion' => '5%',
                'forma_farmaceutica' => 'Crema',
                'presentacion' => 'Tubo 50g',
                'precio_venta' => 12.00,
                'estado' => 'Suspendido',
            ],
        ];

        foreach ($productos as $producto) {
            ProductoTerminado::create($producto);
        }
    }
}
