<?php

namespace App\Filament\Exports;

use App\Models\OrdenProduccion;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrdenProduccionExporter extends Exporter
{
    protected static ?string $model = OrdenProduccion::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('numero_orden')
                ->label('Nº Orden'),

            ExportColumn::make('productoTerminado.nombre')
                ->label('Producto'),

            ExportColumn::make('cantidad_planificada')
                ->label('Cantidad Planificada'),

            ExportColumn::make('fecha_planificada')
                ->label('Fecha Planificada')
                ->formatStateUsing(fn ($state) => $state?->format('d/m/Y')),

            ExportColumn::make('estado')
                ->label('Estado'),

            ExportColumn::make('notas')
                ->label('Notas'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Exportación de órdenes de producción completada con ' . number_format($export->successful_rows) . ' registros.';
    }
}
