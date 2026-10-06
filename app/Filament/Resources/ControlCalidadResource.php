<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControlCalidadResource\Pages;
use App\Models\ControlCalidad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ControlCalidadResource extends Resource
{
    protected static ?string $model = ControlCalidad::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Control de Calidad';
    protected static ?string $modelLabel = 'Análisis de Calidad';
    protected static ?string $navigationGroup = 'Calidad';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver control calidad');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear control calidad');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar control calidad');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar control calidad');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Análisis')
                    ->schema([
                        Forms\Components\Select::make('lote_produccion_id')
                            ->label('Lote a Analizar')
                            ->relationship('loteProduccion', 'numero_lote')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2)
                            ->helperText('Selecciona el lote que se va a analizar'),
                        Forms\Components\TextInput::make('analista')
                            ->label('Analista Responsable')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('fecha_analisis')
                            ->label('Fecha de Análisis')
                            ->required()
                            ->default(now()),
                    ])->columns(2),
                Forms\Components\Section::make('Resultados del Análisis')
                    ->schema([
                        Forms\Components\TextInput::make('ph')
                            ->label('pH')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(14)
                            ->step(0.01)
                            ->helperText('Escala de 0 a 14'),
                        Forms\Components\TextInput::make('pureza_porcentaje')
                            ->label('Pureza (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Porcentaje de pureza del producto'),
                        Forms\Components\Select::make('resultado')
                            ->label('Resultado del Análisis')
                            ->options([
                                'Pendiente' => '⏳ Pendiente',
                                'Cumple' => '✅ Cumple',
                                'No Cumple' => '❌ No Cumple',
                            ])
                            ->default('Pendiente')
                            ->required()
                            ->reactive(),
                    ])->columns(3),
                Forms\Components\Section::make('Observaciones')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones del Analista')
                            ->rows(4)
                            ->columnSpan(2)
                            ->placeholder('Detalles del análisis, desviaciones, notas...'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loteProduccion.numero_lote')
                    ->label('Nº Lote')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('loteProduccion.productoTerminado.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('analista')
                    ->label('Analista')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_analisis')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ph')
                    ->label('pH')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pureza_porcentaje')
                    ->label('Pureza')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('resultado')
                    ->label('Resultado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Cumple' => 'success',
                        'No Cumple' => 'danger',
                        'Pendiente' => 'warning',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Cumple' => 'heroicon-o-check-circle',
                        'No Cumple' => 'heroicon-o-x-circle',
                        'Pendiente' => 'heroicon-o-clock',
                    }),
            ])
            ->filters([
                // Filtro por resultado
                SelectFilter::make('resultado')
                    ->label('Resultado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Cumple' => 'Cumple',
                        'No Cumple' => 'No Cumple',
                    ]),

                // Filtro por analista (búsqueda de texto)
                Filter::make('analista')
                    ->form([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre del analista'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nombre'],
                            fn (Builder $query, $nombre): Builder => $query->where('analista', 'like', "%{$nombre}%"),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['nombre'] ?? null) {
                            return ['Analista: ' . $data['nombre']];
                        }
                        return [];
                    }),

                // Filtro por rango de fechas
                Filter::make('fecha_analisis')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Analizado desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Analizado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_analisis', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_analisis', '<=', $date),
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

                // Filtro por lote
                SelectFilter::make('lote_produccion_id')
                    ->label('Lote')
                    ->relationship('loteProduccion', 'numero_lote')
                    ->searchable()
                    ->preload(),

                // Filtro rápido: Pendientes de revisar
                Filter::make('pendientes')
                    ->label('Pendientes de revisar')
                    ->query(fn (Builder $query): Builder => $query->where('resultado', 'Pendiente')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListControlCalidads::route('/'),
            'create' => Pages\CreateControlCalidad::route('/create'),
            'edit' => Pages\EditControlCalidad::route('/{record}/edit'),
        ];
    }
}
