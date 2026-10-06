<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoTerminadoResource\Pages;
use App\Models\ProductoTerminado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductoTerminadoResource extends Resource
{
    protected static ?string $model = ProductoTerminado::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Productos Terminados';
    protected static ?string $modelLabel = 'Producto Terminado';
    protected static ?string $navigationGroup = 'Inventario';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver productos terminados');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear productos terminados');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar productos terminados');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar productos terminados');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'codigo_producto', 'concentracion'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->nombre;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Código' => $record->codigo_producto,
            'Concentración' => $record->concentracion,
            'Forma' => $record->forma_farmaceutica,
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
                Forms\Components\Section::make('Información del Producto')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre Comercial')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('codigo_producto')
                            ->label('Código de Producto')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('concentracion')
                            ->label('Concentración')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('forma_farmaceutica')
                            ->label('Forma Farmacéutica')
                            ->options([
                                'Tableta' => 'Tableta',
                                'Cápsula' => 'Cápsula',
                                'Jarabe' => 'Jarabe',
                                'Crema' => 'Crema',
                                'Inyectable' => 'Inyectable',
                                'Suspensión' => 'Suspensión',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('presentacion')
                            ->label('Presentación')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('precio_venta')
                            ->label('Precio de Venta (€)')
                            ->numeric()
                            ->prefix('€')
                            ->minValue(0)
                            ->step(0.01),
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'En desarrollo' => 'En desarrollo',
                                'Aprobado' => 'Aprobado',
                                'Suspendido' => 'Suspendido',
                            ])
                            ->default('En desarrollo')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_producto')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('concentracion')
                    ->label('Concentración')
                    ->searchable(),
                Tables\Columns\TextColumn::make('forma_farmaceutica')
                    ->label('Forma')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('precio_venta')
                    ->label('Precio')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aprobado' => 'success',
                        'En desarrollo' => 'warning',
                        'Suspendido' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options(['En desarrollo' => 'En desarrollo', 'Aprobado' => 'Aprobado', 'Suspendido' => 'Suspendido']),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductoTerminados::route('/'),
            'create' => Pages\CreateProductoTerminado::route('/create'),
            'edit' => Pages\EditProductoTerminado::route('/{record}/edit'),
        ];
    }
}
