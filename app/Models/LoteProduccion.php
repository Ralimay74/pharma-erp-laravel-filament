<?php

namespace App\Models;

use App\Observers\LoteProduccionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteProduccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_lote',
        'producto_terminado_id',
        'fecha_fabricacion',
        'fecha_caducidad',
        'cantidad_producida',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_fabricacion' => 'date',
        'fecha_caducidad' => 'date',
    ];

    protected static function booted(): void
    {
        static::observe(LoteProduccionObserver::class);
    }

    public function productoTerminado(): BelongsTo
    {
        return $this->belongsTo(ProductoTerminado::class, 'producto_terminado_id');
    }

    /**
     * Convierte cantidad a kg para comparar con el stock
     */
    private function convertirAKg($cantidad, $unidad): float
    {
        return match ($unidad) {
            'mg' => $cantidad / 1000000,
            'g' => $cantidad / 1000,
            'kg' => $cantidad,
            'ml' => $cantidad / 1000, // Asumiendo densidad ~1
            'L' => $cantidad,
            'unidades' => $cantidad, // No aplica conversión
            default => $cantidad,
        };
    }

    public function calcularMateriasPrimasNecesarias()
    {
        $formulaItems = FormulaItem::where('producto_terminado_id', $this->producto_terminado_id)->get();
        
        $necesarias = [];
        foreach ($formulaItems as $item) {
            $cantidadTotal = $item->cantidad_requerida * $this->cantidad_producida;
            $cantidadEnKg = $this->convertirAKg($cantidadTotal, $item->unidad_medida);
            
            $necesarias[] = [
                'materia_prima' => $item->materiaPrima,
                'cantidad_necesaria' => $cantidadTotal,
                'cantidad_en_kg' => $cantidadEnKg,
                'unidad_medida' => $item->unidad_medida,
            ];
        }
        
        return $necesarias;
    }

    public function validarStock(): array
    {
        $necesarias = $this->calcularMateriasPrimasNecesarias();
        $errores = [];
        
        foreach ($necesarias as $item) {
            $mp = $item['materia_prima'];
            if ($mp->stock_actual < $item['cantidad_en_kg']) {
                $errores[] = "Stock insuficiente de {$mp->nombre}: hay {$mp->stock_actual} kg, se necesitan {$item['cantidad_en_kg']} kg";
            }
        }
        
        return $errores;
    }

    public function descontarStock()
    {
        $necesarias = $this->calcularMateriasPrimasNecesarias();
        
        foreach ($necesarias as $item) {
            $mp = $item['materia_prima'];
            // Descontar en kg (unidad del stock)
            $mp->decrement('stock_actual', $item['cantidad_en_kg']);
        }
    }
}
