<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Cofares S.A.',
                'nif_cif' => 'A-28000001',
                'tipo' => 'Distribuidora',
                'contacto' => 'Carlos Ruiz',
                'email' => 'pedidos@cofares.es',
                'telefono' => '+34 91 555 01 01',
                'direccion' => 'Polígono Industrial La Mina, Calle A, 12',
                'ciudad' => 'Madrid',
                'pais' => 'España',
                'codigo_postal' => '28001',
                'limite_credito' => 50000.00,
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'Hospital Universitario La Paz',
                'nif_cif' => 'Q-28000002',
                'tipo' => 'Hospital',
                'contacto' => 'Dra. Ana Martínez (Farmacia Hospitalaria)',
                'email' => 'farmacia@hulp.es',
                'telefono' => '+34 91 555 02 02',
                'direccion' => 'Paseo de la Castellana, 261',
                'ciudad' => 'Madrid',
                'pais' => 'España',
                'codigo_postal' => '28046',
                'limite_credito' => 100000.00,
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'Farmacéutica del Sur',
                'nif_cif' => 'A-41000003',
                'tipo' => 'Distribuidora',
                'contacto' => 'Pedro López',
                'email' => 'compras@farsur.com',
                'telefono' => '+34 95 555 03 03',
                'direccion' => 'Av. de la Innovación, 45',
                'ciudad' => 'Sevilla',
                'pais' => 'España',
                'codigo_postal' => '41001',
                'limite_credito' => 35000.00,
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'Farmacia Central Gran Vía',
                'nif_cif' => 'B-28000004',
                'tipo' => 'Farmacia',
                'contacto' => 'Lucía Fernández',
                'email' => 'info@farmaciagranvia.es',
                'telefono' => '+34 91 555 04 04',
                'direccion' => 'Gran Vía, 120',
                'ciudad' => 'Madrid',
                'pais' => 'España',
                'codigo_postal' => '28013',
                'limite_credito' => 5000.00,
                'estado' => 'Activo',
            ],
            [
                'nombre' => 'Laboratorios Genéricos S.L.',
                'nif_cif' => 'B-08000005',
                'tipo' => 'Laboratorio',
                'contacto' => 'Javier Torres',
                'email' => 'b2b@labgenericos.es',
                'telefono' => '+34 93 555 05 05',
                'direccion' => 'Parc Científic de Barcelona, Edificio B',
                'ciudad' => 'Barcelona',
                'pais' => 'España',
                'codigo_postal' => '08028',
                'limite_credito' => 25000.00,
                'estado' => 'Activo',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
