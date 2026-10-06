<?php

namespace App\Filament\Exports;

use App\Models\ControlCalidad;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ControlCalidadExporter extends Exporter
{
    protected static ?string $model = ControlCalidad::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('loteProduccion.numero_lote')
                ->label('Nº Lote'),

            ExportColumn::make('loteProduccion.productoTerminado.nombre')
                ->label('Producto'),

            ExportColumn::make('analista')
                ->label('Analista'),

            ExportColumn::make('fecha_analisis')
                ->label('Fecha Análisis')
                ->formatStateUsing(fn ($state) => $state?->format('d/m/Y')),

            ExportColumn::make('ph')
                ->label('pH'),

            ExportColumn::make('pureza_porcentaje')
                ->label('Pureza (%)'),

            ExportColumn::make('resultado')
                ->label('Resultado'),

            ExportColumn::make('observaciones')
                ->label('Observaciones'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Exportación de control de calidad completada con ' . number_format($export->successful_rows) . ' registros.';
    }
}
