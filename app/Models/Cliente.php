<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'nif_cif',
        'tipo',
        'contacto',
        'email',
        'telefono',
        'direccion',
        'ciudad',
        'pais',
        'codigo_postal',
        'limite_credito',
        'estado',
        'notas',
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
    ];
}
