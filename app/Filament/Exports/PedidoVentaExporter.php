<?php

namespace App\Filament\Exports;

use App\Models\PedidoVenta;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PedidoVentaExporter extends Exporter
{
    protected static ?string $model = PedidoVenta::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('numero_pedido')
                ->label('Nº Pedido'),

            ExportColumn::make('cliente.nombre')
                ->label('Cliente'),

            ExportColumn::make('fecha_pedido')
                ->label('Fecha Pedido')
                ->formatStateUsing(fn ($state) => $state?->format('d/m/Y')),

            ExportColumn::make('fecha_entrega_estimada')
                ->label('Fecha Entrega')
                ->formatStateUsing(fn ($state) => $state?->format('d/m/Y')),

            ExportColumn::make('subtotal')
                ->label('Subtotal (€)'),

            ExportColumn::make('iva')
                ->label('IVA (€)'),

            ExportColumn::make('total')
                ->label('Total (€)'),

            ExportColumn::make('estado')
                ->label('Estado'),

            ExportColumn::make('notas')
                ->label('Notas'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Exportación de pedidos completada con ' . number_format($export->successful_rows) . ' registros.';
    }
}
