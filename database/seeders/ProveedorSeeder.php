<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'nombre' => 'Bayer HealthCare Pharmaceuticals',
                'nif_cif' => 'B-12345678',
                'contacto' => 'Juan Pérez',
                'email' => 'ventas.pharma@bayer.com',
                'telefono' => '+34 91 123 45 67',
                'direccion' => 'Av. de la Innovación, 1, Madrid, España',
                'pais' => 'España',
                'certificaciones' => 'GMP, ISO 9001, ISO 14001',
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'BASF Pharma Ingredients',
                'nif_cif' => 'B-87654321',
                'contacto' => 'María González',
                'email' => 'pharma.ing@basf.com',
                'telefono' => '+49 621 123 456',
                'direccion' => 'Carl-Bosch-Str. 38, Ludwigshafen, Alemania',
                'pais' => 'Alemania',
                'certificaciones' => 'GMP, ISO 9001, FDA Registered',
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'Lonza Group AG',
                'nif_cif' => 'CHE-123.456.789',
                'contacto' => 'Hans Müller',
                'email' => 'sales@lonza.com',
                'telefono' => '+41 61 316 81 11',
                'direccion' => 'Münchensteinerstrasse 38, Basel, Suiza',
                'pais' => 'Suiza',
                'certificaciones' => 'GMP, ISO 9001, WHO Prequalification',
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'Merck KGaA',
                'nif_cif' => 'HRB 1234',
                'contacto' => 'Laura Schmidt',
                'email' => 'healthcare@merckgroup.com',
                'telefono' => '+49 6151 72 0',
                'direccion' => 'Frankfurter Str. 250, Darmstadt, Alemania',
                'pais' => 'Alemania',
                'certificaciones' => 'GMP, ISO 14001, ISO 45001',
                'estado' => 'Activo',
            ],
        ];

        foreach ($proveedores as $prov) {
            Proveedor::create($prov);
        }
    }
}
