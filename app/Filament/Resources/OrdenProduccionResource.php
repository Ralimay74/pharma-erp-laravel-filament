<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrdenProduccionResource\Pages;
use App\Models\OrdenProduccion;
use App\Models\LoteProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class OrdenProduccionResource extends Resource
{
    protected static ?string $model = OrdenProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Órdenes de Producción';
    protected static ?string $modelLabel = 'Orden de Producción';
    protected static ?string $navigationGroup = 'Producción';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver ordenes produccion');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear ordenes produccion');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar ordenes produccion');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar ordenes produccion');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Planificación de la Orden')
                    ->schema([
                        Forms\Components\TextInput::make('numero_orden')
                            ->label('Número de Orden')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Select::make('producto_terminado_id')
                            ->label('Producto a Fabricar')
                            ->relationship('productoTerminado', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('cantidad_planificada')
                            ->label('Cantidad Planificada')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\DatePicker::make('fecha_planificada')
                            ->label('Fecha Planificada')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('estado')
                            ->label('Estado de la Orden')
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'En Producción' => 'En Producción',
                                'Completada' => 'Completada',
                                'Cancelada' => 'Cancelada',
                            ])
                            ->default('Pendiente')
                            ->required(),
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas / Instrucciones')
                            ->rows(3)
                            ->columnSpan(2),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_orden')
                    ->label('Nº Orden')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('productoTerminado.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('cantidad_planificada')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_planificada')
                    ->label('Fecha Planif.')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Completada' => 'success',
                        'En Producción' => 'info',
                        'Pendiente' => 'warning',
                        'Cancelada' => 'danger',
                    }),
            ])
            ->filters([
                // Filtro por estado
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En Producción' => 'En Producción',
                        'Completada' => 'Completada',
                        'Cancelada' => 'Cancelada',
                    ]),

                // Filtro por producto
                SelectFilter::make('producto_terminado_id')
                    ->label('Producto')
                    ->relationship('productoTerminado', 'nombre')
                    ->searchable()
                    ->preload(),

                // Filtro por rango de fechas
                Filter::make('fecha_planificada')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Planificada desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Planificada hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_planificada', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_planificada', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) {
                            $indicators[] = 'Desde: ' . $data['desde'];
                        }
                        if ($data['hasta'] ?? null) {
                            $indicators[] = 'Hasta: ' . $data['hasta'];
                        }
                        return $indicators;
                    }),

                // Filtro por cantidad planificada
                Filter::make('cantidad')
                    ->form([
                        Forms\Components\TextInput::make('min')
                            ->label('Cantidad mínima')
                            ->numeric(),
                        Forms\Components\TextInput::make('max')
                            ->label('Cantidad máxima')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min'],
                                fn (Builder $query, $value): Builder => $query->where('cantidad_planificada', '>=', $value),
                            )
                            ->when(
                                $data['max'],
                                fn (Builder $query, $value): Builder => $query->where('cantidad_planificada', '<=', $value),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min'] ?? null) {
                            $indicators[] = 'Cant. mín: ' . $data['min'];
                        }
                        if ($data['max'] ?? null) {
                            $indicators[] = 'Cant. máx: ' . $data['max'];
                        }
                        return $indicators;
                    }),

                // Filtro rápido: Órdenes pendientes
                Filter::make('pendientes')
                    ->label('Solo pendientes')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Pendiente')),

                // Filtro rápido: En producción activa
                Filter::make('en_produccion')
                    ->label('En producción activa')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'En Producción')),

                // Filtro rápido: Completadas
                Filter::make('completadas')
                    ->label('Completadas')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Completada')),

                // Filtro rápido: Canceladas
                Filter::make('canceladas')
                    ->label('Canceladas')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Cancelada')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('convertir_a_lote')
                    ->label('Convertir a Lote')
                    ->icon('heroicon-o-cube-transparent')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Lote de Producción')
                    ->modalDescription('¿Estás seguro de que deseas crear un lote de producción a partir de esta orden? El estado de la orden cambiará a "Completada".')
                    ->action(function (OrdenProduccion $record) {
                        LoteProduccion::create([
                            'numero_lote' => 'LOT-' . $record->numero_orden,
                            'producto_terminado_id' => $record->producto_terminado_id,
                            'fecha_fabricacion' => now(),
                            'fecha_caducidad' => now()->addYears(2),
                            'cantidad_producida' => $record->cantidad_planificada,
                            'estado' => 'En proceso',
                        ]);
                        
                        $record->update(['estado' => 'Completada']);

                        Notification::make()
                            ->title('✅ Lote creado exitosamente')
                            ->body("Se ha generado el lote LOT-{$record->numero_orden} y está en proceso.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (OrdenProduccion $record) => $record->estado === 'Pendiente' || $record->estado === 'En Producción'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrdenProduccions::route('/'),
            'create' => Pages\CreateOrdenProduccion::route('/create'),
            'edit' => Pages\EditOrdenProduccion::route('/{record}/edit'),
        ];
    }
}
