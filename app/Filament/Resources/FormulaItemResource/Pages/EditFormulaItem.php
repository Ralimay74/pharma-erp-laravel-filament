<?php

namespace App\Filament\Resources\FormulaItemResource\Pages;

use App\Filament\Resources\FormulaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormulaItem extends EditRecord
{
    protected static string $resource = FormulaItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
