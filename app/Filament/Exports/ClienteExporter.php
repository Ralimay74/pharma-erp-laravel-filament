<?php

namespace App\Filament\Exports;

use App\Models\Cliente;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ClienteExporter extends Exporter
{
    protected static ?string $model = Cliente::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nombre')
                ->label('Cliente'),

            ExportColumn::make('nif_cif')
                ->label('NIF/CIF'),

            ExportColumn::make('tipo')
                ->label('Tipo'),

            ExportColumn::make('contacto')
                ->label('Contacto'),

            ExportColumn::make('email')
                ->label('Email'),

            ExportColumn::make('telefono')
                ->label('Teléfono'),

            ExportColumn::make('ciudad')
                ->label('Ciudad'),

            ExportColumn::make('pais')
                ->label('País'),

            ExportColumn::make('limite_credito')
                ->label('Límite Crédito (€)'),

            ExportColumn::make('estado')
                ->label('Estado'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Exportación de clientes completada con ' . number_format($export->successful_rows) . ' registros.';
    }
}
