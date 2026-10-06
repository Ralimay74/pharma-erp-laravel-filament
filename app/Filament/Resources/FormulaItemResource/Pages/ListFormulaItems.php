<?php

namespace App\Filament\Resources\FormulaItemResource\Pages;

use App\Filament\Resources\FormulaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormulaItems extends ListRecords
{
    protected static string $resource = FormulaItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
