<?php

namespace App\Filament\Widgets;

use App\Models\LoteProduccion;
use Filament\Widgets\ChartWidget;

class LotesChart extends ChartWidget
{
    protected static ?string $heading = 'Estado de Lotes de Producción';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $enProceso = LoteProduccion::where('estado', 'En proceso')->count();
        $cuarentena = LoteProduccion::where('estado', 'Cuarentena')->count();
        $liberado = LoteProduccion::where('estado', 'Liberado')->count();
        $rechazado = LoteProduccion::where('estado', 'Rechazado')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Lotes',
                    'data' => [$enProceso, $cuarentena, $liberado, $rechazado],
                    'backgroundColor' => ['#3b82f6', '#f59e0b', '#10b981', '#ef4444'],
                ],
            ],
            'labels' => ['En Proceso', 'Cuarentena', 'Liberado', 'Rechazado'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
