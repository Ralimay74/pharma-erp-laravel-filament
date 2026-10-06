<?php

namespace App\Observers;

use App\Models\ControlCalidad;
use Filament\Notifications\Notification;

class ControlCalidadObserver
{
    /**
     * Cuando se CREA un análisis de calidad
     */
    public function created(ControlCalidad $controlCalidad): void
    {
        $this->procesarResultado($controlCalidad);
    }

    /**
     * Cuando se ACTUALIZA un análisis de calidad
     */
    public function updated(ControlCalidad $controlCalidad): void
    {
        // Solo procesar si cambió el resultado
        if ($controlCalidad->isDirty('resultado')) {
            $this->procesarResultado($controlCalidad);
        }
    }

    /**
     * Lógica central para procesar el resultado
     */
    private function procesarResultado(ControlCalidad $controlCalidad): void
    {
        $lote = $controlCalidad->loteProduccion;
        
        if ($controlCalidad->resultado === 'Cumple') {
            // Liberar el lote automáticamente
            $lote->update(['estado' => 'Liberado']);
            
            Notification::make()
                ->title('✅ Lote Liberado')
                ->body("El lote {$lote->numero_lote} ha sido liberado automáticamente y el stock de materias primas ha sido descontado.")
                ->success()
                ->send();
        } elseif ($controlCalidad->resultado === 'No Cumple') {
            // Rechazar el lote
            $lote->update(['estado' => 'Rechazado']);
            
            Notification::make()
                ->title('❌ Lote Rechazado')
                ->body("El lote {$lote->numero_lote} ha sido rechazado por no cumplir los estándares de calidad.")
                ->danger()
                ->send();
        }
    }
}
