<?php

namespace Database\Seeders;

use App\Models\PedidoVenta;
use App\Models\PedidoItem;
use App\Models\Cliente;
use App\Models\ProductoTerminado;
use App\Models\LoteProduccion;
use Illuminate\Database\Seeder;

class PedidoVentaSeeder extends Seeder
{
    public function run(): void
    {
        // Crear algunos lotes liberados para usar en los pedidos
        $lote1 = LoteProduccion::firstOrCreate([
            'numero_lote' => 'LOT-VENTA-001',
        ], [
            'producto_terminado_id' => ProductoTerminado::where('codigo_producto', 'PT-001')->first()->id,
            'fecha_fabricacion' => now()->subDays(30),
            'fecha_caducidad' => now()->addYears(2),
            'cantidad_producida' => 5000,
            'estado' => 'Liberado',
        ]);

        $lote2 = LoteProduccion::firstOrCreate([
            'numero_lote' => 'LOT-VENTA-002',
        ], [
            'producto_terminado_id' => ProductoTerminado::where('codigo_producto', 'PT-002')->first()->id,
            'fecha_fabricacion' => now()->subDays(25),
            'fecha_caducidad' => now()->addYears(2),
            'cantidad_producida' => 3000,
            'estado' => 'Liberado',
        ]);

        // Pedido 1: Cofares (Distribuidora)
        $cofares = Cliente::where('nombre', 'Cofares S.A.')->first();
        if ($cofares) {
            $pedido1 = PedidoVenta::create([
                'numero_pedido' => 'PED-2026-001',
                'cliente_id' => $cofares->id,
                'fecha_pedido' => now()->subDays(5),
                'fecha_entrega_estimada' => now()->addDays(10),
                'estado' => 'Confirmado',
                'notas' => 'Entrega en almacén central Madrid',
            ]);

            $producto1 = ProductoTerminado::where('codigo_producto', 'PT-001')->first();
            $producto2 = ProductoTerminado::where('codigo_producto', 'PT-002')->first();

            if ($producto1) {
                PedidoItem::create([
                    'pedido_venta_id' => $pedido1->id,
                    'producto_terminado_id' => $producto1->id,
                    'lote_produccion_id' => $lote1->id,
                    'cantidad' => 500,
                    'precio_unitario' => $producto1->precio_venta ?? 2.50,
                    'subtotal' => 500 * ($producto1->precio_venta ?? 2.50),
                ]);
            }

            if ($producto2) {
                PedidoItem::create([
                    'pedido_venta_id' => $pedido1->id,
                    'producto_terminado_id' => $producto2->id,
                    'lote_produccion_id' => $lote2->id,
                    'cantidad' => 300,
                    'precio_unitario' => $producto2->precio_venta ?? 3.75,
                    'subtotal' => 300 * ($producto2->precio_venta ?? 3.75),
                ]);
            }

            $pedido1->recalcularTotales();
        }

        // Pedido 2: Hospital La Paz
        $hospital = Cliente::where('nombre', 'Hospital Universitario La Paz')->first();
        if ($hospital) {
            $pedido2 = PedidoVenta::create([
                'numero_pedido' => 'PED-2026-002',
                'cliente_id' => $hospital->id,
                'fecha_pedido' => now()->subDays(2),
                'fecha_entrega_estimada' => now()->addDays(5),
                'estado' => 'En Preparación',
                'notas' => 'Entrega urgente en farmacia hospitalaria',
            ]);

            $producto1 = ProductoTerminado::where('codigo_producto', 'PT-001')->first();

            if ($producto1) {
                PedidoItem::create([
                    'pedido_venta_id' => $pedido2->id,
                    'producto_terminado_id' => $producto1->id,
                    'lote_produccion_id' => $lote1->id,
                    'cantidad' => 200,
                    'precio_unitario' => $producto1->precio_venta ?? 2.50,
                    'subtotal' => 200 * ($producto1->precio_venta ?? 2.50),
                ]);
            }

            $pedido2->recalcularTotales();
        }
    }
}
