<?php

namespace App\Filament\Resources\ProductoTerminadoResource\Pages;

use App\Filament\Resources\ProductoTerminadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductoTerminados extends ListRecords
{
    protected static string $resource = ProductoTerminadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
