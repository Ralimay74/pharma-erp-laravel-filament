<?php

namespace App\Filament\Exports;

use App\Models\Proveedor;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProveedorExporter extends Exporter
{
    protected static ?string $model = Proveedor::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nombre')
                ->label('Proveedor'),

            ExportColumn::make('nif_cif')
                ->label('NIF/CIF'),

            ExportColumn::make('contacto')
                ->label('Contacto'),

            ExportColumn::make('email')
                ->label('Email'),

            ExportColumn::make('telefono')
                ->label('Teléfono'),

            ExportColumn::make('pais')
                ->label('País'),

            ExportColumn::make('certificaciones')
                ->label('Certificaciones'),

            ExportColumn::make('estado')
                ->label('Estado'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Exportación de proveedores completada con ' . number_format($export->successful_rows) . ' registros.';
    }
}
