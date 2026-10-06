<?php

namespace App\Filament\Widgets;

use App\Models\LoteProduccion;
use App\Models\MateriaPrima;
use App\Models\ProductoTerminado;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $mpTotal = MateriaPrima::count();
        $mpCuarentena = MateriaPrima::where('estado', 'Cuarentena')->count();
        $mpStockBajo = MateriaPrima::where('stock_actual', '<', 50)->count();
        $lotesEnProceso = LoteProduccion::where('estado', 'En proceso')->count();
        $lotesLiberados = LoteProduccion::where('estado', 'Liberado')->count();
        $productosAprobados = ProductoTerminado::where('estado', 'Aprobado')->count();

        return [
            Stat::make('Materias Primas', $mpTotal)
                ->description('Total registradas')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('primary'),

            Stat::make('En Cuarentena', $mpCuarentena)
                ->description('Pendientes de análisis')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($mpCuarentena > 0 ? 'warning' : 'success'),

            Stat::make('Stock Bajo', $mpStockBajo)
                ->description('Menos de 50 unidades')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color($mpStockBajo > 0 ? 'danger' : 'success'),

            Stat::make('Lotes en Proceso', $lotesEnProceso)
                ->description('Fabricación activa')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('info'),

            Stat::make('Lotes Liberados', $lotesLiberados)
                ->description('Aptos para venta')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Productos Aprobados', $productosAprobados)
                ->description('En catálogo activo')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),
        ];
    }
}
