<?php

namespace App\Models;

use App\Observers\ControlCalidadObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlCalidad extends Model
{
    use HasFactory;

    protected $fillable = [
        'lote_produccion_id',
        'analista',
        'fecha_analisis',
        'ph',
        'pureza_porcentaje',
        'resultado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_analisis' => 'date',
    ];

    protected static function booted(): void
    {
        static::observe(ControlCalidadObserver::class);
    }

    public function loteProduccion(): BelongsTo
    {
        return $this->belongsTo(LoteProduccion::class);
    }
}
