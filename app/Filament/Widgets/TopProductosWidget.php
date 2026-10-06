<?php

namespace App\Filament\Widgets;

use App\Models\PedidoItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductosWidget extends ChartWidget
{
    protected static ?string $heading = 'Productos Más Vendidos (últimos 30 días)';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $topProductos = PedidoItem::select('producto_terminado_id', DB::raw('SUM(cantidad) as total_unidades'))
            ->whereHas('pedidoVenta', function ($query) {
                $query->where('fecha_pedido', '>=', now()->subDays(30))
                      ->whereNotIn('estado', ['Cancelado']);
            })
            ->groupBy('producto_terminado_id')
            ->orderByDesc('total_unidades')
            ->limit(5)
            ->with('productoTerminado')
            ->get();

        $labels = $topProductos->map(fn ($p) => $p->productoTerminado->nombre ?? 'Desconocido')->toArray();
        $data = $topProductos->pluck('total_unidades')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Unidades vendidas',
                    'data' => $data,
                    'backgroundColor' => ['#06b6d4', '#84cc16', '#eab308', '#f97316', '#ec4899'],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
