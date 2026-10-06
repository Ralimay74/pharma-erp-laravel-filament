<?php

namespace App\Filament\Resources\MateriaPrimaResource\Pages;

use App\Filament\Resources\MateriaPrimaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListMateriaPrimas extends ListRecords
{
    protected static string $resource = MateriaPrimaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make()
                ->label('📊 Exportar a Excel')
                ->color('success'),
        ];
    }
}
