<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir permisos por módulo
        $permisos = [
            'ver materias primas', 'crear materias primas', 'editar materias primas', 'eliminar materias primas',
            'ver productos terminados', 'crear productos terminados', 'editar productos terminados', 'eliminar productos terminados',
            'ver formulas', 'crear formulas', 'editar formulas', 'eliminar formulas',
            'ver lotes produccion', 'crear lotes produccion', 'editar lotes produccion', 'eliminar lotes produccion',
            'ver control calidad', 'crear control calidad', 'editar control calidad', 'eliminar control calidad', 'liberar lotes',
            'ver ordenes produccion', 'crear ordenes produccion', 'editar ordenes produccion', 'eliminar ordenes produccion',
            'ver proveedores', 'crear proveedores', 'editar proveedores', 'eliminar proveedores',
            'ver clientes', 'crear clientes', 'editar clientes', 'eliminar clientes',
            'ver pedidos venta', 'crear pedidos venta', 'editar pedidos venta', 'eliminar pedidos venta',
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios', 'gestionar roles',
        ];

        // Crear todos los permisos
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear rol: Administrador (todos los permisos)
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->syncPermissions(Permission::all());

        // Crear rol: Analista QC
        $analistaQC = Role::firstOrCreate(['name' => 'Analista QC']);
        $analistaQC->syncPermissions([
            'ver control calidad', 'crear control calidad', 'editar control calidad',
            'ver lotes produccion', 'ver materias primas', 'ver productos terminados',
        ]);

        // Crear rol: Operario de Producción
        $operario = Role::firstOrCreate(['name' => 'Operario de Producción']);
        $operario->syncPermissions([
            'ver lotes produccion', 'crear lotes produccion', 'editar lotes produccion',
            'ver formulas', 'ver ordenes produccion', 'crear ordenes produccion', 'editar ordenes produccion',
            'ver materias primas', 'ver productos terminados',
        ]);

        // Crear rol: Comercial
        $comercial = Role::firstOrCreate(['name' => 'Comercial']);
        $comercial->syncPermissions([
            'ver clientes', 'crear clientes', 'editar clientes',
            'ver pedidos venta', 'crear pedidos venta', 'editar pedidos venta',
            'ver productos terminados',
        ]);

        // Asignar rol Administrador al usuario existente
        $adminUser = User::where('email', 'raymar000@gmail.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('Administrador');
        }

        // Crear usuarios de prueba para cada rol
        $usuarios = [
            ['name' => 'Ana García (Analista QC)', 'email' => 'analista@pharmaerp.com', 'password' => bcrypt('password123'), 'rol' => 'Analista QC'],
            ['name' => 'Carlos López (Operario)', 'email' => 'operario@pharmaerp.com', 'password' => bcrypt('password123'), 'rol' => 'Operario de Producción'],
            ['name' => 'María Rodríguez (Comercial)', 'email' => 'comercial@pharmaerp.com', 'password' => bcrypt('password123'), 'rol' => 'Comercial'],
        ];

        foreach ($usuarios as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => $u['password']]
            );
            $user->assignRole($u['rol']);
        }
    }
}
