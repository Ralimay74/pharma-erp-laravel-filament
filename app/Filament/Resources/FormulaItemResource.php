<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormulaItemResource\Pages;
use App\Models\FormulaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormulaItemResource extends Resource
{
    protected static ?string $model = FormulaItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Fórmulas / Recetas';
    protected static ?string $modelLabel = 'Fórmula';
    protected static ?string $navigationGroup = 'Producción';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver formulas');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear formulas');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar formulas');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar formulas');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Composición de la Fórmula')
                    ->schema([
                        Forms\Components\Select::make('producto_terminado_id')
                            ->label('Producto Terminado')
                            ->relationship('productoTerminado', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\Select::make('materia_prima_id')
                            ->label('Materia Prima')
                            ->relationship('materiaPrima', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('cantidad_requerida')
                            ->label('Cantidad Requerida')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
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
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('productoTerminado.nombre')
                    ->label('Producto Terminado')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('materiaPrima.nombre')
                    ->label('Materia Prima')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('cantidad_requerida')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable()
                    ->suffix(fn ($record) => ' ' . $record->unidad_medida),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('producto_terminado_id')
                    ->label('Producto')
                    ->relationship('productoTerminado', 'nombre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListFormulaItems::route('/'),
            'create' => Pages\CreateFormulaItem::route('/create'),
            'edit' => Pages\EditFormulaItem::route('/{record}/edit'),
        ];
    }
}
