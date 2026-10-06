<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenProduccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_orden',
        'producto_terminado_id',
        'cantidad_planificada',
        'fecha_planificada',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_planificada' => 'date',
    ];

    public function productoTerminado(): BelongsTo
    {
        return $this->belongsTo(ProductoTerminado::class, 'producto_terminado_id');
    }
}
