<?php

namespace App\Observers;

use App\Models\LoteProduccion;

class LoteProduccionObserver
{
    /**
     * Cuando un lote cambia su estado a "Liberado", descontar stock
     */
    public function updated(LoteProduccion $lote): void
    {
        if ($lote->isDirty('estado') && $lote->estado === 'Liberado') {
            $lote->descontarStock();
        }
    }
}
