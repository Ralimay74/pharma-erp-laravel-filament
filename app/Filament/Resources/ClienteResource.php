<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $navigationGroup = 'Ventas';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver clientes');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear clientes');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar clientes');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar clientes');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'nif_cif', 'email', 'contacto', 'ciudad'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->nombre;
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'NIF/CIF' => $record->nif_cif,
            'Tipo' => $record->tipo,
            'Ciudad' => $record->ciudad ?? 'N/A',
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
                Forms\Components\Section::make('Datos Fiscales')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Razón Social / Nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('nif_cif')
                            ->label('NIF / CIF')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de Cliente')
                            ->options([
                                'Distribuidora' => 'Distribuidora Farmacéutica',
                                'Hospital' => 'Hospital',
                                'Clínica' => 'Clínica',
                                'Farmacia' => 'Farmacia',
                                'Laboratorio' => 'Laboratorio Externo',
                                'Otro' => 'Otro',
                            ])
                            ->default('Distribuidora')
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('Datos de Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('contacto')
                            ->label('Persona de Contacto')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(3),
                Forms\Components\Section::make('Dirección')
                    ->schema([
                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección')
                            ->rows(2)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('ciudad')
                            ->label('Ciudad')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('codigo_postal')
                            ->label('Código Postal')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('pais')
                            ->label('País')
                            ->default('España')
                            ->maxLength(100),
                    ])->columns(3),
                Forms\Components\Section::make('Condiciones y Estado')
                    ->schema([
                        Forms\Components\TextInput::make('limite_credito')
                            ->label('Límite de Crédito (€)')
                            ->numeric()
                            ->prefix('€')
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                                'Moroso' => 'Moroso',
                            ])
                            ->default('Activo')
                            ->required(),
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas Internas')
                            ->rows(3)
                            ->columnSpan(2),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nif_cif')
                    ->label('NIF/CIF')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Distribuidora' => 'primary',
                        'Hospital' => 'info',
                        'Farmacia' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('contacto')
                    ->label('Contacto')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ciudad')
                    ->label('Ciudad')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('limite_credito')
                    ->label('Límite Crédito')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo' => 'success',
                        'Inactivo' => 'gray',
                        'Moroso' => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Inactivo' => 'Inactivo',
                        'Moroso' => 'Moroso',
                    ]),
                SelectFilter::make('tipo')
                    ->label('Tipo de Cliente')
                    ->options([
                        'Distribuidora' => 'Distribuidora Farmacéutica',
                        'Hospital' => 'Hospital',
                        'Clínica' => 'Clínica',
                        'Farmacia' => 'Farmacia',
                        'Laboratorio' => 'Laboratorio Externo',
                        'Otro' => 'Otro',
                    ]),
                Filter::make('ciudad')
                    ->form([
                        Forms\Components\TextInput::make('nombre')->label('Ciudad'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nombre'],
                            fn (Builder $query, $ciudad) => $query->where('ciudad', 'like', "%{$ciudad}%"),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['nombre'] ?? null) {
                            return ['Ciudad: ' . $data['nombre']];
                        }
                        return [];
                    }),
                Filter::make('pais')
                    ->form([
                        Forms\Components\TextInput::make('nombre')->label('País'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nombre'],
                            fn (Builder $query, $pais) => $query->where('pais', 'like', "%{$pais}%"),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['nombre'] ?? null) {
                            return ['País: ' . $data['nombre']];
                        }
                        return [];
                    }),
                Filter::make('limite_credito')
                    ->form([
                        Forms\Components\TextInput::make('min')->label('Límite mínimo (€)')->numeric(),
                        Forms\Components\TextInput::make('max')->label('Límite máximo (€)')->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['min'], fn (Builder $query, $value) => $query->where('limite_credito', '>=', $value))
                            ->when($data['max'], fn (Builder $query, $value) => $query->where('limite_credito', '<=', $value));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min'] ?? null) $indicators[] = 'Crédito mín: ' . $data['min'] . '€';
                        if ($data['max'] ?? null) $indicators[] = 'Crédito máx: ' . $data['max'] . '€';
                        return $indicators;
                    }),
                Filter::make('contacto')
                    ->form([
                        Forms\Components\TextInput::make('nombre')->label('Persona de contacto'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nombre'],
                            fn (Builder $query, $contacto) => $query->where('contacto', 'like', "%{$contacto}%"),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['nombre'] ?? null) {
                            return ['Contacto: ' . $data['nombre']];
                        }
                        return [];
                    }),
                Filter::make('morosos')
                    ->label('Solo morosos')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Moroso')),
                Filter::make('alto_credito')
                    ->label('Alto crédito (>50.000€)')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Activo')->where('limite_credito', '>', 50000)),
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
