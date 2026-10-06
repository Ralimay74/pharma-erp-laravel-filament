<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MateriaPrima extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo_interno',
        'proveedor_id',
        'stock_actual',
        'stock_minimo',
        'unidad_medida',
        'estado',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<', 'stock_minimo');
    }
}
