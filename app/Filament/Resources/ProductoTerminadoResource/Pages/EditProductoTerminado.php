<?php

namespace App\Filament\Resources\ProductoTerminadoResource\Pages;

use App\Filament\Resources\ProductoTerminadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductoTerminado extends EditRecord
{
    protected static string $resource = ProductoTerminadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
