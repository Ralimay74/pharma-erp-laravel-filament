<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static ?string $modelLabel = 'Proveedor';
    protected static ?string $navigationGroup = 'Compras y Suministros';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('ver proveedores');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('crear proveedores');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('editar proveedores');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('eliminar proveedores');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Fiscal')
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
                        Forms\Components\TextInput::make('pais')
                            ->label('País')
                            ->default('España')
                            ->maxLength(100),
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
                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección Fiscal')
                            ->rows(2)
                            ->columnSpan(2),
                    ])->columns(2),
                Forms\Components\Section::make('Certificaciones y Estado')
                    ->schema([
                        Forms\Components\TextInput::make('certificaciones')
                            ->label('Certificaciones (GMP, ISO, etc.)')
                            ->helperText('Separar por comas. Ej: GMP, ISO 9001, ISO 14001')
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Select::make('estado')
                            ->label('Estado del Proveedor')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                                'Suspendido' => 'Suspendido',
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
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nif_cif')
                    ->label('NIF/CIF')
                    ->searchable()
                    ->sortable(),
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pais')
                    ->label('País')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('certificaciones')
                    ->label('Certificaciones')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo' => 'success',
                        'Inactivo' => 'gray',
                        'Suspendido' => 'danger',
                    }),
            ])
            ->filters([
                // Filtro por estado
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Inactivo' => 'Inactivo',
                        'Suspendido' => 'Suspendido',
                    ]),

                // Filtro por país
                Filter::make('pais')
                    ->form([
                        Forms\Components\TextInput::make('nombre')
                            ->label('País'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nombre'],
                            fn (Builder $query, $pais): Builder => $query->where('pais', 'like', "%{$pais}%"),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['nombre'] ?? null) {
                            return ['País: ' . $data['nombre']];
                        }
                        return [];
                    }),

                // Filtro por persona de contacto
                Filter::make('contacto')
                    ->form([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Persona de contacto'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nombre'],
                            fn (Builder $query, $contacto): Builder => $query->where('contacto', 'like', "%{$contacto}%"),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['nombre'] ?? null) {
                            return ['Contacto: ' . $data['nombre']];
                        }
                        return [];
                    }),

                // Filtro por certificaciones (búsqueda de texto)
                Filter::make('certificaciones')
                    ->form([
                        Forms\Components\TextInput::make('texto')
                            ->label('Certificación (ej: GMP, ISO)'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['texto'],
                            fn (Builder $query, $cert): Builder => $query->where('certificaciones', 'like', "%{$cert}%"),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['texto'] ?? null) {
                            return ['Certificación: ' . $data['texto']];
                        }
                        return [];
                    }),

                // Filtro por email
                Filter::make('email')
                    ->form([
                        Forms\Components\TextInput::make('texto')
                            ->label('Email'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['texto'],
                            fn (Builder $query, $email): Builder => $query->where('email', 'like', "%{$email}%"),
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['texto'] ?? null) {
                            return ['Email: ' . $data['texto']];
                        }
                        return [];
                    }),

                // Filtro rápido: Proveedores activos
                Filter::make('activos')
                    ->label('Solo activos')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Activo')),

                // Filtro rápido: Proveedores suspendidos
                Filter::make('suspendidos')
                    ->label('Suspendidos')
                    ->query(fn (Builder $query): Builder => $query->where('estado', 'Suspendido')),

                // Filtro rápido: Con certificación GMP
                Filter::make('con_gmp')
                    ->label('Con certificación GMP')
                    ->query(fn (Builder $query): Builder => $query->where('certificaciones', 'like', '%GMP%')),
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
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
