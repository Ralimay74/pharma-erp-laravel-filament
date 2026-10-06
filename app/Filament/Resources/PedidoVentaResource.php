<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoVentaResource\Pages;
use App\Models\PedidoVenta;
use App\Models\LoteProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PedidoVentaResource extends Resource
{
    protected static ?string $model = PedidoVenta::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pedidos de Venta';
    protected static ?string $modelLabel = 'Pedido de Venta';
    protected static ?string $navigationGroup = 'Ventas';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver pedidos venta');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear pedidos venta');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar pedidos venta');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar pedidos venta');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['numero_pedido'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->numero_pedido;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Cliente' => $record->cliente->nombre ?? 'N/A',
            'Total' => number_format($record->total, 2) . ' €',
            'Estado' => $record->estado,
        ];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('edit', ['record' => $record]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del Pedido')
                    ->schema([
                        Forms\Components\TextInput::make('numero_pedido')
                            ->label('Número de Pedido')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->columnSpan(2),
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\DatePicker::make('fecha_pedido')
                            ->label('Fecha del Pedido')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('fecha_entrega_estimada')
                            ->label('Fecha de Entrega Estimada')
                            ->required(),
                        Forms\Components\Select::make('estado')
                            ->label('Estado del Pedido')
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'Confirmado' => 'Confirmado',
                                'En Preparación' => 'En Preparación',
                                'Enviado' => 'Enviado',
                                'Facturado' => 'Facturado',
                                'Cancelado' => 'Cancelado',
                            ])
                            ->default('Pendiente')
                            ->required(),
                    ])->columns(3),
                Forms\Components\Section::make('Líneas de Producto')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('producto_terminado_id')
                                    ->label('Producto')
                                    ->relationship('productoTerminado', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $producto = \App\Models\ProductoTerminado::find($state);
                                            if ($producto) {
                                                $set('precio_unitario', $producto->precio_venta ?? 0);
                                            }
                                        }
                                    })
                                    ->columnSpan(2),
                                Forms\Components\Select::make('lote_produccion_id')
                                    ->label('Lote (Trazabilidad)')
                                    ->options(function () {
                                        return LoteProduccion::where('estado', 'Liberado')->pluck('numero_lote', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Solo lotes liberados disponibles')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $precio = $get('precio_unitario') ?? 0;
                                        $set('subtotal', $state * $precio);
                                    }),
                                Forms\Components\TextInput::make('precio_unitario')
                                    ->label('Precio Unitario (€)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $cantidad = $get('cantidad') ?? 0;
                                        $set('subtotal', $state * $cantidad);
                                    }),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal (€)')
                                    ->numeric()
                                    ->readOnly()
                                    ->dehydrated()
                                    ->default(0),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->columnSpan(2),
                    ]),
                Forms\Components\Section::make('Totales')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')->label('Subtotal (€)')->numeric()->readOnly()->dehydrated()->default(0)->prefix('€'),
                        Forms\Components\TextInput::make('iva')->label('IVA 21% (€)')->numeric()->readOnly()->dehydrated()->default(0)->prefix('€'),
                        Forms\Components\TextInput::make('total')->label('Total (€)')->numeric()->readOnly()->dehydrated()->default(0)->prefix('€')->extraAttributes(['class' => 'text-xl font-bold']),
                    ])->columns(3),
                Forms\Components\Section::make('Notas')
                    ->schema([
                        Forms\Components\Textarea::make('notas')->label('Notas del Pedido')->rows(3)->columnSpan(2),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_pedido')->label('Nº Pedido')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('cliente.nombre')->label('Cliente')->searchable()->sortable()->limit(20),
                Tables\Columns\TextColumn::make('fecha_pedido')->label('Fecha')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('EUR')->sortable(),
                Tables\Columns\TextColumn::make('estado')->label('Estado')->badge()->color(fn (string $state): string => match ($state) {
                    'Facturado' => 'success', 'Enviado' => 'info', 'En Preparación' => 'primary', 'Confirmado' => 'warning', 'Pendiente' => 'gray', 'Cancelado' => 'danger',
                }),
            ])
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(['Pendiente' => 'Pendiente', 'Confirmado' => 'Confirmado', 'En Preparación' => 'En Preparación', 'Enviado' => 'Enviado', 'Facturado' => 'Facturado', 'Cancelado' => 'Cancelado']),
                SelectFilter::make('cliente_id')->label('Cliente')->relationship('cliente', 'nombre')->searchable()->preload(),
                Filter::make('fecha_pedido')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Pedido desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Pedido hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn (Builder $query, $date) => $query->whereDate('fecha_pedido', '>=', $date))
                            ->when($data['hasta'] ?? null, fn (Builder $query, $date) => $query->whereDate('fecha_pedido', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) $indicators[] = 'Desde: ' . $data['desde'];
                        if ($data['hasta'] ?? null) $indicators[] = 'Hasta: ' . $data['hasta'];
                        return $indicators;
                    }),
                Filter::make('pendientes')
                    ->label('Solo pendientes')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Pendiente')),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\ViewAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidoVentas::route('/'),
            'create' => Pages\CreatePedidoVenta::route('/create'),
            'edit' => Pages\EditPedidoVenta::route('/{record}/edit'),
        ];
    }
}
