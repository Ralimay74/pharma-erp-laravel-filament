<?php

namespace App\Filament\Widgets;

use App\Models\PedidoVenta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IngresosMesWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $mesActual = now()->month;
        $anioActual = now()->year;

        $pedidosMes = PedidoVenta::whereMonth('fecha_pedido', $mesActual)
            ->whereYear('fecha_pedido', $anioActual)
            ->whereNotIn('estado', ['Cancelado'])
            ->get();

        $totalMes = $pedidosMes->sum('total');
        $numPedidos = $pedidosMes->count();
        $pedidosPendientes = $pedidosMes->where('estado', 'Pendiente')->count();
        $ticketMedio = $numPedidos > 0 ? $totalMes / $numPedidos : 0;

        // Comparativa con mes anterior
        $mesAnterior = now()->subMonth()->month;
        $anioAnterior = now()->subMonth()->year;
        $totalMesAnterior = PedidoVenta::whereMonth('fecha_pedido', $mesAnterior)
            ->whereYear('fecha_pedido', $anioAnterior)
            ->whereNotIn('estado', ['Cancelado'])
            ->sum('total');

        $variacion = $totalMesAnterior > 0 
            ? round((($totalMes - $totalMesAnterior) / $totalMesAnterior) * 100, 1) 
            : 0;

        return [
            Stat::make('Ingresos del Mes', number_format($totalMes, 2) . ' €')
                ->description($variacion >= 0 ? '+' . $variacion . '% vs mes anterior' : $variacion . '% vs mes anterior')
                ->descriptionIcon($variacion >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($variacion >= 0 ? 'success' : 'danger'),

            Stat::make('Pedidos del Mes', $numPedidos)
                ->description($pedidosPendientes . ' pendientes')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Ticket Medio', number_format($ticketMedio, 2) . ' €')
                ->description('Por pedido')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }
}
