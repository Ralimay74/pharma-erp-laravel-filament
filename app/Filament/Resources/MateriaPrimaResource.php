<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MateriaPrimaResource\Pages;
use App\Models\MateriaPrima;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class MateriaPrimaResource extends Resource
{
    protected static ?string $model = MateriaPrima::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Materias Primas';
    protected static ?string $modelLabel = 'Materia Prima';
    protected static ?string $navigationGroup = 'Inventario';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver materias primas');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear materias primas');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar materias primas');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar materias primas');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'codigo_interno'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->nombre;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Código' => $record->codigo_interno,
            'Stock' => $record->stock_actual . ' ' . $record->unidad_medida,
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
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre de la Materia Prima')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('codigo_interno')
                            ->label('Código Interno')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\Select::make('proveedor_id')
                            ->label('Proveedor')
                            ->relationship('proveedor', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                    ])->columns(2),
                Forms\Components\Section::make('Stock y Control')
                    ->schema([
                        Forms\Components\TextInput::make('stock_actual')
                            ->label('Stock Actual')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01),
                        Forms\Components\TextInput::make('stock_minimo')
                            ->label('Stock Mínimo (Alerta)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01),
                        Forms\Components\Select::make('unidad_medida')
                            ->label('Unidad de Medida')
                            ->options([
                                'mg' => 'Miligramos (mg)',
                                'g' => 'Gramos (g)',
                                'kg' => 'Kilogramos (kg)',
                                'ml' => 'Mililitros (ml)',
                                'L' => 'Litros (L)',
                                'unidades' => 'Unidades',
                            ])
                            ->default('kg')
                            ->required(),
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Cuarentena' => 'En Cuarentena',
                                'Inactivo' => 'Inactivo',
                            ])
                            ->default('Activo')
                            ->required(),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_interno')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->suffix(fn ($record) => ' ' . $record->unidad_medida)
                    ->color(fn ($record) => $record->stock_actual < $record->stock_minimo ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('stock_minimo')
                    ->label('Mínimo')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo' => 'success',
                        'Cuarentena' => 'warning',
                        'Inactivo' => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(['Activo' => 'Activo', 'Cuarentena' => 'Cuarentena', 'Inactivo' => 'Inactivo']),
                SelectFilter::make('proveedor_id')
                    ->label('Proveedor')
                    ->relationship('proveedor', 'nombre')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('unidad_medida')
                    ->label('Unidad de Medida')
                    ->options(['mg' => 'mg', 'g' => 'g', 'kg' => 'kg', 'ml' => 'ml', 'L' => 'L', 'unidades' => 'Unidades']),
                Filter::make('stock_bajo')
                    ->label('Stock bajo (debajo del mínimo)')
                    ->query(fn (Builder $query): Builder => $query->stockBajo()),
                Filter::make('cuarentena')
                    ->label('En Cuarentena')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Cuarentena')),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMateriaPrimas::route('/'),
            'create' => Pages\CreateMateriaPrima::route('/create'),
            'edit' => Pages\EditMateriaPrima::route('/{record}/edit'),
        ];
    }
}
