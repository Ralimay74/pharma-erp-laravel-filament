<?php

namespace App\Filament\Widgets;

use App\Models\LoteProduccion;
use App\Models\MateriaPrima;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class AlertasWidget extends Widget
{
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.alertas-widget';

    public Collection $alertasStock;
    public Collection $alertasCaducidad;

    public function mount(): void
    {
        $this->alertasStock = MateriaPrima::stockBajo()->get();

        $this->alertasCaducidad = LoteProduccion::where('estado', 'Liberado')
            ->where('fecha_caducidad', '<=', now()->addDays(90))
            ->where('fecha_caducidad', '>=', now())
            ->orderBy('fecha_caducidad', 'asc')
            ->get();
    }
}
