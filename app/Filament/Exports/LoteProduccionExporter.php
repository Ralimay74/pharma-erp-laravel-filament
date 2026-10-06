<?php

namespace App\Filament\Exports;

use App\Models\LoteProduccion;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class LoteProduccionExporter extends Exporter
{
    protected static ?string $model = LoteProduccion::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('numero_lote')
                ->label('Nº Lote'),

            ExportColumn::make('productoTerminado.nombre')
                ->label('Producto Terminado'),

            ExportColumn::make('fecha_fabricacion')
                ->label('Fecha de Fabricación')
                ->formatStateUsing(fn ($state) => $state?->format('d/m/Y')),

            ExportColumn::make('fecha_caducidad')
                ->label('Fecha de Caducidad')
                ->formatStateUsing(fn ($state) => $state?->format('d/m/Y')),

            ExportColumn::make('cantidad_producida')
                ->label('Cantidad Producida'),

            ExportColumn::make('estado')
                ->label('Estado'),

            ExportColumn::make('observaciones')
                ->label('Observaciones de Calidad'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Exportación de lotes completada con ' . number_format($export->successful_rows) . ' registros.';
    }
}
