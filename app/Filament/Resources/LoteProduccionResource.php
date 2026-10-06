<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoteProduccionResource\Pages;
use App\Models\LoteProduccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class LoteProduccionResource extends Resource
{
    protected static ?string $model = LoteProduccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Lotes de Producción';
    protected static ?string $modelLabel = 'Lote de Producción';
    protected static ?string $navigationGroup = 'Producción';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver lotes produccion');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear lotes produccion');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar lotes produccion');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar lotes produccion');
    }

    // === BÚSQUEDA GLOBAL ===
    public static function getGloballySearchableAttributes(): array
    {
        return ['numero_lote'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->numero_lote;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Producto' => $record->productoTerminado->nombre ?? 'N/A',
            'Estado' => $record->estado,
            'Caducidad' => $record->fecha_caducidad->format('d/m/Y'),
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
                Forms\Components\Section::make('Información del Lote')
                    ->schema([
                        Forms\Components\TextInput::make('numero_lote')
                            ->label('Número de Lote')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Select::make('producto_terminado_id')
                            ->label('Producto Terminado')
                            ->relationship('productoTerminado', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                    ])->columns(2),
                Forms\Components\Section::make('Fechas y Cantidades')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_fabricacion')
                            ->label('Fecha de Fabricación')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('fecha_caducidad')
                            ->label('Fecha de Caducidad')
                            ->required(),
                        Forms\Components\TextInput::make('cantidad_producida')
                            ->label('Cantidad Producida')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])->columns(3),
                Forms\Components\Section::make('Estado y Control de Calidad')
                    ->schema([
                        Forms\Components\Select::make('estado')
                            ->label('Estado del Lote')
                            ->options([
                                'En proceso' => 'En proceso',
                                'Cuarentena' => 'En Cuarentena',
                                'Liberado' => 'Liberado (Apto para venta)',
                                'Rechazado' => 'Rechazado',
                            ])
                            ->default('En proceso')
                            ->required(),
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones de Calidad')
                            ->rows(3)
                            ->columnSpan(2),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_lote')
                    ->label('Nº Lote')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('productoTerminado.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('fecha_fabricacion')
                    ->label('F. Fabricación')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_caducidad')
                    ->label('F. Caducidad')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($state) => $state < now() ? 'danger' : null),
                Tables\Columns\TextColumn::make('cantidad_producida')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Liberado' => 'success',
                        'En proceso' => 'info',
                        'Cuarentena' => 'warning',
                        'Rechazado' => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'En proceso' => 'En proceso',
                        'Cuarentena' => 'Cuarentena',
                        'Liberado' => 'Liberado',
                        'Rechazado' => 'Rechazado',
                    ]),
                SelectFilter::make('producto_terminado_id')
                    ->label('Producto')
                    ->relationship('productoTerminado', 'nombre')
                    ->searchable()
                    ->preload(),
                Filter::make('fecha_fabricacion_desde')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Fabricado desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Fabricado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn (Builder $query, $date) => $query->whereDate('fecha_fabricacion', '>=', $date))
                            ->when($data['hasta'] ?? null, fn (Builder $query, $date) => $query->whereDate('fecha_fabricacion', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) $indicators[] = 'Desde: ' . $data['desde'];
                        if ($data['hasta'] ?? null) $indicators[] = 'Hasta: ' . $data['hasta'];
                        return $indicators;
                    }),
                Filter::make('fecha_caducidad_desde')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Caduca desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Caduca hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn (Builder $query, $date) => $query->whereDate('fecha_caducidad', '>=', $date))
                            ->when($data['hasta'] ?? null, fn (Builder $query, $date) => $query->whereDate('fecha_caducidad', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) $indicators[] = 'Caduca desde: ' . $data['desde'];
                        if ($data['hasta'] ?? null) $indicators[] = 'Caduca hasta: ' . $data['hasta'];
                        return $indicators;
                    }),
                Filter::make('proximos_caducar')
                    ->label('Próximos a caducar (30 días)')
                    ->query(fn (Builder $query): Builder => $query->where('fecha_caducidad', '<=', now()->addDays(30))->where('fecha_caducidad', '>=', now())),
                Filter::make('caducados')
                    ->label('Ya caducados')
                    ->query(fn (Builder $query): Builder => $query->where('fecha_caducidad', '<', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListLoteProduccions::route('/'),
            'create' => Pages\CreateLoteProduccion::route('/create'),
            'edit' => Pages\EditLoteProduccion::route('/{record}/edit'),
        ];
    }
}
