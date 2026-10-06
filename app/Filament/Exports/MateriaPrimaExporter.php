<?php

namespace App\Filament\Exports;

use App\Models\MateriaPrima;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MateriaPrimaExporter extends Exporter
{
    protected static ?string $model = MateriaPrima::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('codigo_interno')
                ->label('Código'),

            ExportColumn::make('nombre')
                ->label('Nombre'),

            ExportColumn::make('proveedor.nombre')
                ->label('Proveedor'),

            ExportColumn::make('stock_actual')
                ->label('Stock Actual'),

            ExportColumn::make('stock_minimo')
                ->label('Stock Mínimo'),

            ExportColumn::make('unidad_medida')
                ->label('Unidad'),

            ExportColumn::make('estado')
                ->label('Estado'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Exportación de materias primas completada con ' . number_format($export->successful_rows) . ' registros.';
    }
}
