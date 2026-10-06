<?php

namespace App\Filament\Widgets;

use App\Models\MateriaPrima;
use Filament\Widgets\ChartWidget;

class StockMPChart extends ChartWidget
{
    protected static ?string $heading = 'Stock de Materias Primas (kg)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $materiasPrimas = MateriaPrima::where('unidad_medida', 'kg')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Stock (kg)',
                    'data' => $materiasPrimas->pluck('stock_actual')->toArray(),
                    'backgroundColor' => '#8b5cf6',
                ],
            ],
            'labels' => $materiasPrimas->pluck('nombre')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
