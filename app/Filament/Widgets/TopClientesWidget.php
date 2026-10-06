<?php

namespace App\Filament\Widgets;

use App\Models\PedidoVenta;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopClientesWidget extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Clientes (últimos 30 días)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $topClientes = PedidoVenta::select('cliente_id', DB::raw('SUM(total) as total_ventas'))
            ->where('fecha_pedido', '>=', now()->subDays(30))
            ->whereNotIn('estado', ['Cancelado'])
            ->groupBy('cliente_id')
            ->orderByDesc('total_ventas')
            ->limit(5)
            ->with('cliente')
            ->get();

        $labels = $topClientes->map(fn ($p) => $p->cliente->nombre ?? 'Desconocido')->toArray();
        $data = $topClientes->pluck('total_ventas')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Ventas (€)',
                    'data' => $data,
                    'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
